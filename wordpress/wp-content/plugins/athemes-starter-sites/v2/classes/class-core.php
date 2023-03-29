<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    Athemes Starter Sites
 * @subpackage Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Athemes_Starter_Sites' ) ) {
	/**
	 * Main Core Class
	 */
	class Athemes_Starter_Sites {

		/**
		 * The theme name
		 *
		 * @var array $theme.
		 */
		public $theme = '';

		/**
		 * Initial
		 */
		public function init() {

			// Includes.
			require_once ATSS_PATH . 'v2/classes/class-demos.php';
			require_once ATSS_PATH . 'v2/classes/class-widget-importer.php';
			require_once ATSS_PATH . 'v2/classes/class-customizer-importer.php';
			require_once ATSS_PATH . 'v2/classes/class-importer.php';

			// Actions.
			add_action( 'plugins_loaded', array( $this, 'theme_configs' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 15 );

		}

		/**
		 * Load theme config files
		 */
		public function theme_configs() {

			$theme  = wp_get_theme();
			$parent = ( get_template_directory() !== get_stylesheet_directory() && $theme->parent() ) ? $theme->parent() : $theme;

			if ( 'Botiga' === $theme->name || 'Botiga' === $parent->name ) {
				require_once ATSS_PATH . 'v2/themes/botiga.php';
			}

		}

		/**
		 * This function will register scripts and styles for admin dashboard.
		 *
		 * @param string $page Current page.
		 */
		public function admin_enqueue_scripts( $hook ) {

			// Demos.
			$demos = apply_filters( 'atss_register_demos_list', array() );

			if ( ! empty( $demos ) ) {
				foreach ( $demos as $demo_id => $demo ) {
					unset( $demos[ $demo_id ]['import'] );
				}
			}

			// Settings.
			$settings = apply_filters( 'atss_register_demos_settings', array() );

			// Theme.
			$theme = wp_get_theme();
			$theme = ( get_template_directory() !== get_stylesheet_directory() && $theme->parent() ) ? $theme->parent() : $theme;

			wp_enqueue_script( 'athemes-starter-sites-v2', ATSS_URL . 'v2/assets/js/script.min.js', array( 'jquery', 'wp-util', 'underscore' ), '2.0.0', true );

			wp_localize_script( 'athemes-starter-sites-v2', 'atss_localize', array(
				'ajax_url'          => admin_url( 'admin-ajax.php' ),
				'plugin_url'        => ATSS_URL,
				'nonce'             => wp_create_nonce( 'nonce' ),
				'demos'             => $demos,
				'theme_name'        => $theme->name,
				'imported'          => get_option( 'atss_current_starter', '' ),
				'settings'          => $settings,
				'i18n'              => array(
					'import_failed'   => esc_html__( 'Something went wrong, contact support.', 'athemes-starter-sites' ),
					'import_finished' => esc_html__( 'Finished!', 'athemes-starter-sites' ),
					'invalid_email'   => esc_html__( 'Enter a valid email address!', 'athemes-starter-sites' ),
					'tweet_text'      => esc_html__( sprintf( 'I just built my ecommerce website in {0} seconds with %s theme by @athemesdotcom. It was so easy!', $theme->name ), 'athemes-starter-sites' ),
				),
			) );

			// Select2.
			wp_enqueue_style( 'athemes-starter-sites-v2', ATSS_URL . 'v2/assets/css/style.min.css', array(), '2.0.0' );

		}

		public function current_starter( $theme, $demo_id ) {

			$current = get_option( 'atss_current_starter' );

			if ( $current === $demo_id ) {
				return false;
			}
			
			wp_remote_get( add_query_arg( array( 'theme' => $theme, 'demo_id' => $demo_id ), 'https://www.athemes.com/reports/starters.php' ),
				array(
					'timeout'    => 30,
					'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . ';'
				)
			);

			update_option( 'atss_current_starter', $demo_id );

		}

	}

	/**
	 * The main function responsible for returning the one true atss Instance to functions everywhere.
	 * Use this function like you would a global variable, except without needing to declare the global.
	 *
	 * Example: $atss = atss();
	 */
	function atss() {

		// Globals.
		global $atss_instance;

		// Init.
		if ( ! isset( $atss_instance ) ) {
			$atss_instance = new Athemes_Starter_Sites();
			$atss_instance->init();
		}

		return $atss_instance;
	}

	// Initialize.
	atss();

}