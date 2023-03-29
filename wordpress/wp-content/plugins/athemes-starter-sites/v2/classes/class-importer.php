<?php
/**
 * Main class of Athemes Sites Importer plugin.
 *
 * @package    Athemes Starter Sites
 * @subpackage Core
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Importer Class
 */
class Athemes_Starter_Sites_Importer {

	/**
	 * The demos of page.
	 *
	 * @var array $demos The demos.
	 */
	public $demos = array();

	/**
	 * Time in milliseconds, marking the beginning of the import.
	 *
	 * @var float
	 */
	private $microtime;

	/**
	 * Singleton instance
	 *
	 * @var Athemes_Starter_Sites_Import
	 */
	private static $instance;

	/**
	 * Get singleton instance.
	 *
	 * @return Athemes_Starter_Sites_Import
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'init' ) );
	}

	/**
	 * Initialize plugin.
	 */
	public function init() {

		$this->demos = apply_filters( 'atss_register_demos_list', array() );

		add_action( 'upload_mimes', array( $this, 'add_custom_mimes' ) );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'real_mime_type_for_xml' ), 10, 4 );

		add_action( 'wp_ajax_atss_import_start', array( $this, 'atss_import_start' ) );
		add_action( 'wp_ajax_atss_import_clean', array( $this, 'atss_import_clean' ) );
		add_action( 'wp_ajax_atss_import_plugin', array( $this, 'ajax_import_plugin' ) );
		add_action( 'wp_ajax_atss_import_contents', array( $this, 'ajax_import_contents' ) );
		add_action( 'wp_ajax_atss_import_widgets', array( $this, 'ajax_import_widgets' ) );
		add_action( 'wp_ajax_atss_import_customizer', array( $this, 'ajax_import_customizer' ) );
		add_action( 'wp_ajax_atss_import_finish', array( $this, 'ajax_import_finish' ) );

		// Hide wpdb errors for avoid wp_send_json brokens.
		if ( isset( $_POST['action'] ) && $_POST['action'] === 'atss_import_plugin' && ( WP_DEBUG || WP_DEBUG_DISPLAY ) ) {
			global $wpdb;
			$wpdb->hide_errors();
		}

	}

	/**
	 * Add custom mimes for the uploader.
	 *
	 * @param array $mimes The mimes.
	 */
	public function add_custom_mimes( $mimes ) {

		// Allow SVG files.
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';

		// Allow XML files.
		$mimes['xml'] = 'text/xml';

		// Allow JSON files.
		$mimes['json'] = 'application/json';

		return $mimes;

	}

	/**
	 * Filters the "real" file type of the given file.
	 *
	 * @param array  $wp_check_filetype_and_ext The wp_check_filetype_and_ext.
	 * @param string $file                      The file.
	 * @param string $filename                  The filename.
	 * @param array  $mimes                     The mimes.
	 */
	public function real_mime_type_for_xml( $wp_check_filetype_and_ext, $file, $filename, $mimes ) {

		if ( '.xml' === substr( $filename, -4 ) ) {
			$wp_check_filetype_and_ext['ext']  = 'xml';
			$wp_check_filetype_and_ext['type'] = 'text/xml';
		}

		return $wp_check_filetype_and_ext;

	}

	/**
	 * Get plugin status.
	 *
	 * @param string $plugin_path Plugin path.
	 */
	public function get_plugin_status( $plugin_path ) {

		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_path ) ) {
			return 'not_installed';
		} elseif ( in_array( $plugin_path, (array) get_option( 'active_plugins', array() ), true ) || is_plugin_active_for_network( $plugin_path ) ) {
			return 'active';
		} else {
			return 'inactive';
		}

	}

	/**
	 * Install a plugin.
	 *
	 * @param string $plugin_slug Plugin slug.
	 */
	public function install_plugin( $plugin_slug ) {

		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		if ( ! function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}
		if ( ! class_exists( 'WP_Upgrader' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		if ( false === filter_var( $plugin_slug, FILTER_VALIDATE_URL ) ) {
			$api = plugins_api(
				'plugin_information',
				array(
					'slug'   => $plugin_slug,
					'fields' => array(
						'short_description' => false,
						'sections'          => false,
						'requires'          => false,
						'rating'            => false,
						'ratings'           => false,
						'downloaded'        => false,
						'last_updated'      => false,
						'added'             => false,
						'tags'              => false,
						'compatibility'     => false,
						'homepage'          => false,
						'donate_link'       => false,
					),
				)
			);

			$download_link = $api->download_link;
		} else {
			$download_link = $plugin_slug;
		}

		// Use AJAX upgrader skin instead of plugin installer skin.
		// ref: function wp_ajax_install_plugin().
		$upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );

		$install = $upgrader->install( $download_link );

		if ( false === $install ) {
			return false;
		} else {
			return true;
		}

	}

	/**
	 * Activate a plugin.
	 *
	 * @param string $plugin_path Plugin path.
	 */
	public function activate_plugin( $plugin_path ) {

		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}

		$activate = activate_plugin( $plugin_path, '', false, true );

		if ( is_wp_error( $activate ) ) {
			return false;
		} else {
			return true;
		}

	}

	/**
	 * Start import.
	 */
	public function atss_import_start() {

		check_ajax_referer( 'nonce', 'nonce' );

		/**
		 * Variables.
		 */
		$demo_id = ( isset( $_POST['demo_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['demo_id'] ) ) : '';

		if ( ! $demo_id || ! isset( $this->demos[ $demo_id ] ) ) {
			wp_send_json_error( esc_html__( 'Invalid demo id.', 'athemes-starter-sites' ) );
		}

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( esc_html__( 'You are not permitted to start demo.', 'athemes-starter-sites' ) );
		}

		/**
		 * Action hook.
		 */
		do_action( 'atss_import_start' );

		/**
		 * Return successful AJAX.
		 */
		wp_send_json_success();

	}

	/**
	 * Clean previous import.
	 */
	public function atss_import_clean() {

		check_ajax_referer( 'nonce', 'nonce' );

		/**
		 * Variables.
		 */
		$demo_id = ( isset( $_POST['demo_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['demo_id'] ) ) : '';

		if ( ! $demo_id || ! isset( $this->demos[ $demo_id ] ) ) {
			wp_send_json_error( esc_html__( 'Invalid demo id.', 'athemes-starter-sites' ) );
		}

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( esc_html__( 'You are not permitted to clean previous import.', 'athemes-starter-sites' ) );
		}

		/**
		 * Suspend bunches of stuff in WP core.
		 */
		wp_suspend_cache_invalidation( true );

		global $wpdb;

		/**
		 * Delete posts.
		 */
		$query  = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s", '_athemes_sites_imported_post' );
		$result = $wpdb->get_col( $query );

		if ( ! empty( $result ) ) {
			foreach ( $result as $post_id ) {
				$post_type = get_post_type( $post_id );
				if ( $post_type === 'elementor_library' ) {
					$_GET['force_delete_kit'] = true;
				}
				wp_delete_post( $post_id, true );
			}
		}

		/**
		 * Delete terms.
		 */
		$query  = $wpdb->prepare( "SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key = %s", '_athemes_sites_imported_term' );
		$result = $wpdb->get_col( $query );

		if ( ! empty( $result ) ) {
			foreach ( $result as $term_id ) {
				$term = get_term( $term_id );
				if ( ! is_wp_error( $term ) && ! empty( $term ) && is_object( $term ) ) {
					wp_delete_term( $term->term_id, $term->taxonomy );
				}
			}
		}

		/**
		 * Re-enable stuff in core
		 */
		wp_suspend_cache_invalidation( false );

		/**
		 * Clean widget settings.
		 */
		$this->clean_widget_settings();

		/**
		 * Clean customizer settings.
		 */
		$this->clean_customizer_settings();

		/**
		 * Action hook.
		 */
		do_action( 'atss_import_clean' );

		/**
		 * Return successful AJAX.
		 */
		wp_send_json_success();

	}

	/**
	 * Clean widget settings. 
	 */
	public function clean_widget_settings() {

		$imported_widgets = get_option( '_athemes_sites_imported_widgets', array() );

		if ( ! empty( $imported_widgets ) ) {

			$imported_widget_ids = array();

			foreach ( $imported_widgets as $imported_widget ) {
				if ( ! empty( $imported_widget ) && is_array( $imported_widget ) ) {
					$imported_widget_ids = array_merge( $imported_widget_ids, $imported_widget );
				}
			}

			$sidebars_widgets = get_option( 'sidebars_widgets', array() );

			if ( ! empty( $imported_widget_ids ) && ! empty( $sidebars_widgets ) ) {
				foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
					if ( ! empty( $widgets ) && is_array( $widgets ) ) {
						$widgets = (array) $widgets;
						foreach ( $widgets as $widget_id ) {
							if ( in_array( $widget_id, $imported_widget_ids, true ) ) {
								$sidebars_widgets['wp_inactive_widgets'][] = $widget_id;
								$sidebars_widgets[ $sidebar_id ] = array_diff( $sidebars_widgets[ $sidebar_id ], array( $widget_id ) );
							}
						}
					}
				}
				update_option( 'sidebars_widgets', $sidebars_widgets );
			}

			delete_option( '_athemes_sites_imported_widgets' );

		}

	}

	/**
	 * Clean customizer settings. 
	 */
	public function clean_customizer_settings() {

		/**
		 * Clean imported customizer mods.
		 */
		$imported_customizer_mods = get_option( '_athemes_sites_imported_customizer_mods', array() );

		if ( ! empty( $imported_customizer_mods ) ) {

			foreach ( $imported_customizer_mods as $mod_key => $mod_name ) {
				remove_theme_mod( $mod_key );
			}

			remove_theme_mods();

			delete_option( '_athemes_sites_imported_customizer_mods' );

		}

		/**
		 * Clean imported customizer options.
		 */
		$imported_customizer_options = get_option( '_athemes_sites_imported_customizer_options', array() );

		if ( ! empty( $imported_customizer_options ) ) {

			foreach ( $imported_customizer_options as $option_key => $option_name ) {
				delete_option( $option_key );
			}

			delete_option( '_athemes_sites_imported_customizer_options' );

		}

	}

	/**
	 * AJAX callback to install and activate a plugin.
	 */
	public function ajax_import_plugin() {

		check_ajax_referer( 'nonce', 'nonce' );

		/**
		 * Variables.
		 */
		$demo_id = ( isset( $_POST['demo_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['demo_id'] ) ) : '';
		$slug    = ( isset( $_POST['slug'] ) ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
		$path    = ( isset( $_POST['path'] ) ) ? sanitize_text_field( wp_unslash( $_POST['path'] ) ) : '';

		if ( ! $demo_id || ! isset( $this->demos[ $demo_id ] ) ) {
			wp_send_json_error( esc_html__( 'Invalid demo id.', 'athemes-starter-sites' ) );
		}

		if ( empty( $slug ) ) {
			wp_send_json_error( esc_html__( 'Unknown slug in a plugin.', 'athemes-starter-sites' ) );
		}

		if ( empty( $path ) ) {
			wp_send_json_error( esc_html__( 'Unknown path in a plugin.', 'athemes-starter-sites' ) );
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( esc_html__( 'Insufficient permissions to install the plugin.', 'athemes-starter-sites' ) );
		}

		if ( 'not_installed' === $this->get_plugin_status( $path ) ) {

			$this->install_plugin( $slug );

			$this->activate_plugin( $path );

		} elseif ( 'inactive' === $this->get_plugin_status( $path ) ) {

			$this->activate_plugin( $path );

		}

		/**
		 * Action hook.
		 */
		do_action( 'atss_import_plugin', $slug, $path );

		/**
		 * Return successful AJAX.
		 */
		if ( 'active' === $this->get_plugin_status( $path ) ) {
			wp_send_json_success();
		}

		wp_send_json_error( esc_html__( 'Failed to initialize or activate importer plugin.', 'athemes-starter-sites' ) );

	}

	/**
	 * AJAX callback to import contents and media files from contents.xml.
	 */
	public function ajax_import_contents() {

		check_ajax_referer( 'nonce', 'nonce' );

		/**
		 * Variables.
		 */
		$demo_id      = ( isset( $_POST['demo_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['demo_id'] ) ) : '';
		$builder_type = ( isset( $_POST['builder_type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['builder_type'] ) ) : '';
		$content_type = ( isset( $_POST['content_type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['content_type'] ) ) : '';

		if ( ! $demo_id || ! isset( $this->demos[ $demo_id ] ) ) {
			wp_send_json_error( esc_html__( 'Invalid demo id.', 'athemes-starter-sites' ) );
		}

		if ( ! isset( $this->demos[ $demo_id ]['import'][ $builder_type ] ) ) {
			wp_send_json_error( esc_html__( 'Invalid builder type.', 'athemes-starter-sites' ) );
		}

		if ( ! isset( $this->demos[ $demo_id ]['import'][ $builder_type ]['content'] ) ) {
			wp_send_json_error( esc_html__( 'The url address of the demo content is not specified.', 'athemes-starter-sites' ) );
		}

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( esc_html__( 'You are not permitted to import contents.', 'athemes-starter-sites' ) );
		}

		$xml_file_url = $this->demos[ $demo_id ]['import'][ $builder_type ]['content'];

		$xml_file_path = get_transient( 'atss_importer_data' );

		if ( ! $xml_file_path || ! file_exists( $xml_file_path ) ) {

			/**
			 * Download contents.xml
			 */
			if ( ! function_exists( 'download_url' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			// Set timeout.
			$timeout_seconds = 5;

			// Download file to temp dir.
			$temp_file = download_url( $xml_file_url, $timeout_seconds );

			if ( is_wp_error( $temp_file ) ) {
				wp_send_json_error( esc_html__( 'Failed to download temporary demo xml file.', 'athemes-starter-sites' ) );
			}

			// Array based on $_FILE as seen in PHP file uploads.
			$file_args = array(
				'name'     => basename( $xml_file_url ),
				'tmp_name' => $temp_file,
				'error'    => 0,
				'size'     => @filesize( $temp_file ),
			);

			$overrides = array(
				'test_form'   => false,
				'test_size'   => true,
				'test_upload' => true,
				'mimes'       => array(
					'xml'  => 'text/xml',
				),
			);

			// Move the temporary file into the uploads directory.
			$download_response = wp_handle_sideload( $file_args, $overrides );

			// Error when downloading XML file.
			if ( isset( $download_response['error'] ) ) {
				wp_send_json_error( esc_html__( 'Failed to download demo xml file.', 'athemes-starter-sites' ) );
			}

			// Define the downloaded contents.xml file path.
			$xml_file_path = $download_response['file'];

			set_transient( 'atss_importer_data', $xml_file_path, HOUR_IN_SECONDS );

		}

		/**
		 * Import content and media files using WXR Importer.
		 */
		if ( ! class_exists( 'WP_Importer' ) ) {
			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true );
			}
			require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		}

		/**
		 * Import Core.
		 */
		require_once ATSS_PATH . 'v2/vendor/wp-content-importer-v2/WPImporterLogger.php';
		require_once ATSS_PATH . 'v2/vendor/wp-content-importer-v2/WPImporterLoggerCLI.php';
		require_once ATSS_PATH . 'v2/vendor/wp-content-importer-v2/WXRImportInfo.php';
		require_once ATSS_PATH . 'v2/vendor/wp-content-importer-v2/WXRImporter.php';
		require_once ATSS_PATH . 'v2/vendor/wp-content-importer-v2/Logger.php';

		/**
		 * Prepare the importer.
		 */
		// Time to run the import!
		set_time_limit( 0 );

		$this->microtime = microtime( true );

		// Are we allowed to create users?
		add_filter( 'wxr_importer.pre_process.user', '__return_null' );

		// Check, if we need to send another AJAX request and set the importing author to the current user.
		add_filter( 'wxr_importer.pre_process.post', array( $this, 'ajax_request_maybe' ) );

		// Post content replace attachment urls
  	add_filter( 'wxr_importer.pre_process.post', array( $this, 'post_content_replace_attachment_urls' ) );

		// Post meta replace attachment urls
  	add_filter( 'wxr_importer.pre_process.post_meta', array( $this, 'post_meta_replace_attachment_urls' ), 10, 2 );

		// Track imported post
  	add_filter( 'wxr_importer.processed.post', array( $this, 'track_imported_post' ) );

		// Track imported term
  	add_filter( 'wxr_importer.processed.term', array( $this, 'track_imported_term' ) );

		// Convert images to placeholder
		if ( $content_type === 'placeholder' ) {
  		add_filter( 'atss_importer.processed.attachment', array( $this, 'convert_attachment_to_placeholder' ) );
		}

		// WooCommerce product attributes registration.
		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'wxr_importer.pre_process.term', array( $this, 'woocommerce_product_attributes_registration' ) );
			add_filter( 'add_term_metadata', array( $this, 'woocommerce_product_attributes_filter' ), 10, 5 );
		}
		

		// Set the WordPress Importer v2 as the importer used in this plugin.
		// More: https://github.com/humanmade/WordPress-Importer.
		$importer = new ATSS_WXRImporter( array(
			'fetch_attachments' => true,
			'default_author'    => get_current_user_id(),
		) );

		// Logger options for the logger used in the importer.
		$logger_options = apply_filters( 'atss_logger_options', array(
			'logger_min_level' => 'warning',
		) );

		// Configure logger instance and set it to the importer.
		$logger            = new ATSS_Logger();
		$logger->min_level = $logger_options['logger_min_level'];

		// Set logger.
		$importer->set_logger( $logger );

		/**
		 * Process import.
		 */
		$importer->import( $xml_file_path );

		// Is error ?.
		if ( is_wp_error( $importer ) ) {
			wp_send_json_error( esc_html__( 'An error occurred while importing contents.', 'athemes-starter-sites' ) );
		}

		if ( $logger->error_output ) {
			wp_send_json_error( $logger->error_output );
		}

		/**
		 * Action hook.
		 */
		do_action( 'atss_import_contents' );

		/**
		 * Return successful AJAX.
		 */
		wp_send_json_success();

	}

	/**
	 * Check if we need to create a new AJAX request, so that server does not timeout.
	 *
	 * @param array $data current post data.
	 * @return array
	 */
	public function ajax_request_maybe( $data ) {

		$time = microtime( true ) - $this->microtime;

		// We should make a new ajax call, if the time is right.
		if ( $time > apply_filters( 'atss_time_for_one_ajax_call', 300 ) ) {

			$response = array(
				'success' => true,
				'status'  => 'newAJAX',
				'message' => 'Time for new AJAX request!: ' . $time,
			);

			// Send the request for a new AJAX call.
			wp_send_json( $response );

		}

		if ( ! empty( $data ) && is_array( $data ) ) {

			// Set importing author to the current user.
			// Fixes the [WARNING] Could not find the author for ... log warning messages.
			$current_user_obj = wp_get_current_user();

			$data['post_author'] = $current_user_obj->user_login;

		}

		return $data;

	}

	/**
	 * Replace attachment urls.
	 */
	public function replace_attachment_urls( $content ) {

		if ( is_serialized( $content ) ) {
			return $content;
		}

		preg_match_all( '/(?:http(?:s?):)(?:[\/\\\\\\\\|.|\w|\s|-])*\.(?:jpg|jpeg|jpe|png|gif|webp|svg)/m', $content, $image_urls );

		if ( ! empty( $image_urls[0] ) ) {
			
			$image_urls = array_unique( $image_urls[0] );

			foreach ( $image_urls as $image_url ) {

				$clean_url = wp_unslash( $image_url );

				if ( ! strpos( $clean_url, '/uploads/' ) ) {
					continue;
				}

				$url_parts = parse_url( $clean_url );

				if ( ! isset( $url_parts['host'] ) ) {
					continue;
				}

				$url_parts['path'] = explode( '/', $url_parts['path'] );
				$url_parts['path'] = array_slice( $url_parts['path'], -3 );

				$uploads_dir = wp_get_upload_dir();
				$uploads_url = $uploads_dir['baseurl'];

				$new_url = esc_url( $uploads_url . '/' . join( '/', $url_parts['path'] ) );
				$content = str_replace( $image_url, $new_url, $content );

			}

		}

		return $content;

	}

	/**
	 * Post content replace attachment urls.
	 */
	public function post_content_replace_attachment_urls( $data ) {

		if ( ! empty( $data ) && ! empty( $data['post_content'] ) ) {
			$data['post_content'] = $this->replace_attachment_urls( $data['post_content'] );
		}

		return $data;

	}

	/**
	 * Post meta replace attachment urls.
	 */
	public function post_meta_replace_attachment_urls( $meta_item, $post_id ) {

		if ( ! empty( $meta_item ) ) {

			// Replace mega menu attachments urls.
			if ( $meta_item['key'] === '_is_mega_menu_item_content_custom_html' && ! empty( $meta_item['value'] ) ) {
				$meta_item['value'] = $this->replace_attachment_urls( $meta_item['value'] );
			}

			// Replace elementor attachments urls.
			if ( $meta_item['key'] === '_elementor_data' && ! empty( $meta_item['value'] ) ) {
				$meta_item['value'] = $this->replace_attachment_urls( maybe_unserialize( $meta_item['value'] ) );
			}

	    // Set elementor default kit.
	    if ( $meta_item['key'] === '_elementor_template_type' && $meta_item['value'] === 'kit' ) {
	      update_option( 'elementor_active_kit', $post_id );
	    }

		}

		return $meta_item;

	}

	/**
	 * Track imported post for clean previous install.
	 */
	public function track_imported_post( $post_id = 0 ) {

		update_post_meta( $post_id, '_athemes_sites_imported_post', true );

	}

	/**
	 * Track imported term for clean previous install.
	 */
	public function track_imported_term( $term_id = 0 ) {

		update_term_meta( $term_id, '_athemes_sites_imported_term', true );

	}

	/**
	 * Convert attachment to placeholder.
	 */
	public function convert_attachment_to_placeholder( $data ) {

		if ( ! empty( $data['file'] ) ) {

			$imagedata = @getimagesize( $data['file'] );

			if ( empty( $imagedata ) ) {
				return $data;
			}

			list( $image_width, $image_height, $image_type ) = $imagedata;

			if ( empty( $image_width ) || empty( $image_height ) || empty( $image_type ) ) {
				return $data;
			}

			$image = @imagecreatetruecolor( $image_width, $image_height );

			@imagefilter( $image, IMG_FILTER_COLORIZE, 240, 240, 240 );

			switch ( $image_type ) {

				case IMAGETYPE_GIF:
					@imagegif( $image, $data['file'] );
				break;

				case IMAGETYPE_PNG:
					@imagepng( $image, $data['file'] );
				break;

				case IMAGETYPE_JPEG:
					@imagejpeg( $image, $data['file'] );
				break;

			}

		}

		return $data;

	}

	/**
	 *
	 * Hook into the pre-process term filter of the content import and register the
	 * custom WooCommerce product attributes, so that the terms can then be imported normally.
	 *
	 * This should probably be removed once the WP importer 2.0 support is added in WooCommerce.
	 *
	 * Fixes: [WARNING] Failed to import pa_size L warnings in content import.
	 * Code from: woocommerce/includes/admin/class-wc-admin-importers.php (ver 2.6.9).
	 *
	 * Github issue: https://github.com/awesomemotive/one-click-demo-import/issues/71
	 *
	 * @param  array $date The term data to import.
	 * @return array       The unchanged term data.
	 *
	 */
	public function woocommerce_product_attributes_registration( $data ) {

		global $wpdb;

		if ( strstr( $data['taxonomy'], 'pa_' ) ) {

			if ( ! taxonomy_exists( $data['taxonomy'] ) ) {

				$attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $data['taxonomy'] ) );
				$attribute_type = 'select';

				// To do: Generate .xml import file with "attribute_type".
				if( $attribute_name === 'color' ) {
					$attribute_type = 'color';
				} else if( $attribute_name === 'size' ) {
					$attribute_type = 'button';
				} else if( $attribute_name === 'image' ) {
					$attribute_type = 'image';
				}

				// Create the taxonomy
				if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {

					$attribute = array(
						'attribute_label'   => ucwords( str_replace( '-', ' ', $attribute_name ) ),
						'attribute_name'    => $attribute_name,
						'attribute_type'    => $attribute_type,
						'attribute_orderby' => 'menu_order',
						'attribute_public'  => 0
					);

					$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );

					delete_transient( 'wc_attribute_taxonomies' );

				}

				// Register the taxonomy now so that the import works!
				register_taxonomy(
					$data['taxonomy'],
					apply_filters( 'woocommerce_taxonomy_objects_' . $data['taxonomy'], array( 'product' ) ),
					apply_filters( 'woocommerce_taxonomy_args_' . $data['taxonomy'], array(
						'hierarchical' => true,
						'show_ui'      => false,
						'query_var'    => true,
						'rewrite'      => false,
					) )
				);

			}

		}

		return $data;

	}

	/**
	 * WooCommerce product attribute filter.
	 */
	public function woocommerce_product_attributes_filter( $check, $object_id, $meta_key, $meta_value, $unique ) {

		$meta_keys = array(
			'product_attribute_color',
			'product_attribute_image',
		);

		if ( in_array( $meta_key, $meta_keys ) && $meta_value === '' ) {
			return false;
		}

		return $check;
	}

	/**
	 * AJAX callback to import widgets on all sidebars from widgets.json.
	 */
	public function ajax_import_widgets() {

		check_ajax_referer( 'nonce', 'nonce' );

		/**
		 * Variables.
		 */
		$demo_id      = ( isset( $_POST['demo_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['demo_id'] ) ) : '';
		$builder_type = ( isset( $_POST['builder_type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['builder_type'] ) ) : '';

		if ( ! $demo_id || ! isset( $this->demos[ $demo_id ] ) ) {
			wp_send_json_error( esc_html__( 'Invalid demo id.', 'athemes-starter-sites' ) );
		}

		if ( ! isset( $this->demos[ $demo_id ]['import'][ $builder_type ] ) ) {
			wp_send_json_error( esc_html__( 'Invalid builder type.', 'athemes-starter-sites' ) );
		}

		if ( ! isset( $this->demos[ $demo_id ]['import'][ $builder_type ]['widgets'] ) ) {
			wp_send_json_error( esc_html__( 'No widgets WIE file specified.', 'athemes-starter-sites' ) );
		}

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( esc_html__( 'You are not permitted to import widgets.', 'athemes-starter-sites' ) );
		}

		$file_url = $this->demos[ $demo_id ]['import'][ $builder_type ]['widgets'];

		/**
		 * Process widgets.json.
		 */
		// Get JSON data from widgets.json.
		$raw = wp_remote_get( wp_unslash( $file_url ) );

		// Abort if widgets.json response code is not successful.
		if ( 200 != wp_remote_retrieve_response_code( $raw ) ) {
			wp_send_json_error( esc_html__( 'Failed to load widget demo file.', 'athemes-starter-sites' ) );
		}

	 	// Clean widget settings.
		$this->clean_widget_settings();

		// Decode raw JSON string to associative array.
		$data = json_decode( wp_remote_retrieve_body( $raw ) );

		$data = map_deep( $data, array( $this, 'replace_attachment_urls' ) );

		$widgets = new ATSS_Widget_Importer();

		// Import.
		$results = $widgets->import( $data );

		if ( is_wp_error( $results ) ) {
			$error_message = $results->get_error_message();
			wp_send_json_error( $error_message );
		}

		/**
		 * Action hook.
		 */

		// Get all available widgets site supports.
		$available_widgets = ATSS_Widget_Importer::available_widgets();

		// Get all existing widget instances.
		$widget_instances = array();

		foreach ( $available_widgets as $widget_data ) {
			$widget_instances[ $widget_data['id_base'] ] = get_option( 'widget_' . $widget_data['id_base'] );
		}

		// Sidebar Widgets
		$sidebar_widgets = get_option( 'sidebars_widgets', array() );

		update_option( '_athemes_sites_imported_widgets', $sidebar_widgets, 'no' );

		do_action( 'atss_import_widgets', $sidebar_widgets, $widget_instances );

		/**
		 * Return successful AJAX.
		 */
		wp_send_json_success();

	}

	/**
	 * AJAX callback to import customizer settings from customizer.json.
	 */
	public function ajax_import_customizer() {

		check_ajax_referer( 'nonce', 'nonce' );

		/**
		 * Variables.
		 */
		$demo_id      = ( isset( $_POST['demo_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['demo_id'] ) ) : '';
		$builder_type = ( isset( $_POST['builder_type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['builder_type'] ) ) : '';

		if ( ! $demo_id || ! isset( $this->demos[ $demo_id ] ) ) {
			wp_send_json_error( esc_html__( 'Invalid demo id.', 'athemes-starter-sites' ) );
		}

		if ( ! isset( $this->demos[ $demo_id ]['import'][ $builder_type ] ) ) {
			wp_send_json_error( esc_html__( 'Invalid builder type.', 'athemes-starter-sites' ) );
		}

		if ( ! isset( $this->demos[ $demo_id ]['import'][ $builder_type ]['customizer'] ) ) {
			wp_send_json_error( esc_html__( 'The url address of the demo customizer is not specified.', 'athemes-starter-sites' ) );
		}

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( esc_html__( 'You are not permitted to import customizer.', 'athemes-starter-sites' ) );
		}

		$file_url = $this->demos[ $demo_id ]['import'][ $builder_type ]['customizer'];

		/**
		 * Process customizer.json.
		 */
		// Get JSON data from customizer.json.
		$raw = wp_remote_get( wp_unslash( $file_url ) );

		// Abort if customizer.json response code is not successful.
		if ( 200 != wp_remote_retrieve_response_code( $raw ) ) {
			wp_send_json_error( esc_html__( 'Failed to load customizer demo file.', 'athemes-starter-sites' ) );
		}

		// Clean customizer settings.
		$this->clean_customizer_settings();

		// Decode raw JSON string to associative array.
		$data = maybe_unserialize( wp_remote_retrieve_body( $raw ), true );

		$data = map_deep( $data, array( $this, 'replace_attachment_urls' ) );

		$customizer = new ATSS_Customizer_Importer();

		// Import.
		$results = $customizer->import( $data );

		if ( is_wp_error( $results ) ) {
			wp_send_json_error( esc_html__( 'An error occurred while importing customizer.', 'athemes-starter-sites' ) );
		}

		/**
		 * Action hook.
		 */
		do_action( 'atss_import_customizer', $data );

		/**
		 * Return successful AJAX.
		 */
		wp_send_json_success();

	}

	/**
	 * AJAX callback to finish import.
	 */
	public function ajax_import_finish() {

		check_ajax_referer( 'nonce', 'nonce' );

		/**
		 * Get Demo ID.
		 */
		$demo_id      = ( isset( $_POST['demo_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['demo_id'] ) ) : '';
		$builder_type = ( isset( $_POST['builder_type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['builder_type'] ) ) : '';
		$content_type = ( isset( $_POST['content_type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['content_type'] ) ) : '';

		if ( ! $demo_id || ! isset( $this->demos[ $demo_id ] ) ) {
			wp_send_json_error( esc_html__( 'Invalid demo id.', 'athemes-starter-sites' ) );
		}

		if ( ! isset( $this->demos[ $demo_id ]['import'][ $builder_type ] ) ) {
			wp_send_json_error( esc_html__( 'Invalid builder type.', 'athemes-starter-sites' ) );
		}

		/**
		 * Delete imported XML file.
		 */
		$xml_file_path = get_transient( 'atss_importer_data' );

		if ( $xml_file_path && file_exists( $xml_file_path ) ) {
			unlink( $xml_file_path );
			delete_transient( 'atss_importer_data' );
		}

		/**
		 * Elementor clear cache.
		 */
		if ( class_exists( 'Elementor\Plugin' ) ) {
			Elementor\Plugin::$instance->files_manager->clear_cache();
		}

		/**
		 * Update nav menu term count for avoid missing menu items.
		 */
		$nav_menu_terms = get_terms( array(
			'taxonomy'   => 'nav_menu',
			'hide_empty' => false,
			'meta_key'   => '_athemes_sites_imported_term',
		) );

		$nav_menu_term_ids = array();

		if ( ! is_wp_error( $nav_menu_terms ) && ! empty( $nav_menu_terms ) ) {
			foreach ( $nav_menu_terms as $nav_menu_term ) {
				if ( empty( $nav_menu_term->count ) ) {
					$nav_menu_term_ids[] = $nav_menu_term->term_id;
				}
			}
		}

		if ( ! empty( $nav_menu_term_ids ) ) {
			wp_update_term_count_now( $nav_menu_term_ids, 'nav_menu' );
		}

		/**
		 * Update stock status to instock (needed for has variation products).
		 */
		if ( class_exists( 'WooCommerce' ) ) {
			$products = get_posts( array(
				'post_type'      => 'product',
				'posts_per_page' => -1,
				'meta_key'       => '_athemes_sites_imported_post',
			) );
			if ( ! is_wp_error( $products ) && ! empty( $products ) ) {
				foreach ( $products as $product ) {
					update_post_meta( $product->ID, '_stock_status', 'instock' );
				}
			}
			// Flush rewrite rules for shop/listing page.
			update_option( 'woocommerce_queue_flush_rewrite_rules', 'yes' );
		}

		/**
		 * Action hook.
		 */
		do_action( 'atss_finish_import', $demo_id );

		/**
		 * Return successful AJAX.
		 */
		wp_send_json_success();

	}

}

new Athemes_Starter_Sites_Importer();
