<?php
/**
 * Demos Page
 *
 * @package Athemes Starter Sites
 * @subpackage Core
 * @version    1.0.0
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Demos Page class.
 */
class ATSS_Demos_Page {

	/**
	 * The settings of page.
	 *
	 * @var array $settings The settings.
	 */
	public $settings = array(
		'has_pro'    => false,
		'pro_label'  => '',
		'pro_link'   => '#',
		'categories' => array(),
		'builders'   => array(),
	);

	/**
	 * The demos of page.
	 *
	 * @var array $demos The demos.
	 */
	public $demos = array();

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'set_demos' ) );
		add_action( 'init', array( $this, 'set_settings' ) );

		add_action( 'admin_notices', array( $this, 'html_notice' ) );
		add_action( 'admin_footer', array( $this, 'preview_template' ) );
		add_action( 'admin_footer', array( $this, 'import_template' ) );

		add_action( 'atss_starter_sites', array( $this, 'html_demos' ) );

		add_action( 'wp_ajax_atss_import_data', array( $this, 'import_data' ) );
		add_action( 'wp_ajax_atss_html_import_data', array( $this, 'html_import_data' ) );
		add_action( 'wp_ajax_atss_dismissed_handler', array( $this, 'ajax_dismissed_handler' ) );

		add_action( 'atss_plugin_activation', array( $this, 'reset_notices' ) );
		add_action( 'atss_plugin_deactivation', array( $this, 'reset_notices' ) );

	}

	/**
	 * Settings
	 *
	 * @param array $settings The settings.
	 */
	public function set_settings() {
		$this->settings = apply_filters( 'atss_register_demos_settings', $this->settings );
	}

	/**
	 * Demos
	 *
	 * @param array $demos The demos.
	 */
	public function set_demos( $demos ) {
		$this->demos = apply_filters( 'atss_register_demos_list', $this->demos );
	}

	/**
	 * Import Data
	 */
	public function import_data() {

		check_ajax_referer( 'nonce', 'nonce' );

    try{

			$demo_id        = ( isset( $_POST['demo_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['demo_id'] ) ) : '';
			$builder        = ( isset( $_POST['builder'] ) ) ? sanitize_text_field( wp_unslash( $_POST['builder'] ) ) : '';
			$content_type   = ( isset( $_POST['content_type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['content_type'] ) ) : '';
			$import_content = ( isset( $_POST['import_content'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['import_content'] ) ) : '';

			if ( ! $demo_id || ! isset( $this->demos[ $demo_id ] ) ) {
        throw new Exception( esc_html__( 'Invalid demo id.', 'athemes-starter-sites' ) );
      }

			// Reset import data.
			// delete_transient( 'atss_importer_data' );

			wp_send_json_success( $this->demos[ $demo_id ] );

    } catch( Exception $e ) {

			wp_send_json_error( $e->getMessage() );

    }

	}

	/**
	 * HTML Demos
	 */
	public function html_demos() {

		if ( empty( $this->demos ) ) {
			return;
		}

		$current_demo = get_option( 'atss_current_starter', '' );

		?>
			<div class="atss">

				<div class="atss-demos">

					<?php foreach ( $this->demos as $demo_id => $demo ) : ?>

						<?php

							// Variables.
							$name    = ( ! empty( $demo['name'] ) ) ? $demo['name'] : '';
							$type    = ( ! empty( $demo['type'] ) ) ? $demo['type'] : '';
							$preview = ( ! empty( $demo['preview'] ) ) ? $demo['preview'] : '';

							// Categories.
							$categories = '[]';

							if ( ! empty( $demo['categories'] ) ) {
								foreach ( $demo['categories'] as $category ) {
									$categories .= sprintf( '[%s]', $category );
								}
							}

							// Builders.
							$builders = '[]';

							if ( ! empty( $demo['builders'] ) ) {
								foreach ( $demo['builders'] as $builder ) {
									$builders .= sprintf( '[%s]', $builder );
								}
							}

							$imported_class = ( $current_demo === $demo_id ) ? ' atss-demo-item-imported' : '';

						?>

						<div class="atss-demo-item<?php echo esc_attr( $imported_class ); ?>" data-type="<?php echo esc_attr( $type ); ?>" data-categories="<?php echo esc_attr( $categories ); ?>" data-builders="<?php echo esc_attr( $builders ); ?>">

							<div class="atss-demo-image">
								<?php if ( ! empty( $demo['thumbnail'] ) ) : ?>
									<figure>
										<img src="<?php echo esc_url( $demo['thumbnail'] ); ?>">
									</figure>
								<?php endif; ?>
								<?php if ( ! empty( $demo['builders'] ) && count( $demo['builders'] ) > 1 ) : ?>
									<div class="atss-demo-quick-import">
										<?php foreach ( $demo['builders'] as $builder ) : ?>
											<a href="#" class="atss-import-open-button" data-demo-id="<?php echo esc_attr( $demo_id ); ?>" data-builder="<?php echo esc_attr( $builder ); ?>" data-quick="yes"><?php echo esc_html( ucfirst( $builder ) ); ?></a>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>

							<div class="atss-demo-data">

								<div class="atss-demo-info">
									<?php if ( ! empty( $demo['name'] ) ) : ?>
										<div class="atss-demo-name">
											<?php echo esc_html( $demo['name'] ); ?>
											<?php if ( ! $this->settings['has_pro'] ) : ?>
												<?php if ( $demo['type'] === 'free' ) : ?>
													<div class="atss-demo-badge atss-demo-badge-free">free</div>
												<?php else : ?>
													<div class="atss-demo-badge atss-demo-badge-pro">pro</div>
												<?php endif; ?>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>

								<div class="atss-demo-buttons">
									<div class="atss-demo-preview">
										<?php if ( ! empty( $demo['preview'] ) ) : ?>
											<a href="<?php echo esc_url( $preview ); ?>" class="atss-demo-preview-button button button-secondary"><?php esc_html_e( 'Preview', 'athemes-starter-sites' ); ?></a>
										<?php endif; ?>
									</div>
									<div class="atss-demo-actions">
										<?php if ( $this->settings['has_pro'] || $demo['type'] === 'free' ) : ?>
											<a href="#" class="atss-import-open-button button button-primary" data-demo-id="<?php echo esc_attr( $demo_id ); ?>"><?php esc_html_e( 'Import', 'athemes-starter-sites' ); ?></a>
										<?php endif; ?>
										<?php if ( ! $this->settings['has_pro'] && $demo['type'] === 'pro' ) : ?>
											<a href="<?php echo esc_url( $this->settings['pro_link'] ); ?>" target="_blank" class="atss-demo-pro-link-button button button-primary"><?php echo esc_html( $this->settings['pro_label'] ); ?></a>
										<?php endif; ?>
									</div>
								</div>

							</div>

						</div>

					<?php endforeach; ?>

				</div>

				<div class="atss-import"></div>
				<div class="atss-preview"></div>

			</div>
		<?php
	}

	/**
	 * Preview Template
	 */
	public function preview_template() {
		?>
			<script type="text/html" id="tmpl-atss-preview">
					<div class="atss-preview-header">
						<div class="atss-preview-header-left">
							<div class="atss-preview-header-column atss-preview-header-logo">
								<a href="<?php echo esc_url( 'https://athemes.com/' ); ?>" target="_blank">
									<figure>
										<img width="96px" height="24px" src="{{ window.atss_localize.plugin_url }}v2/assets/img/logo.svg" alt="<?php esc_html_e( 'aThemes', 'athemes-starter-sites' ); ?>">
									</figure>
								</a>
							</div>
							<div class="atss-preview-header-column atss-preview-header-arrow">
								<a href="#" class="atss-preview-header-arrow-prev"><i class="dashicons dashicons-arrow-left-alt2"></i></a>
							</div>
							<div class="atss-preview-header-column atss-preview-header-arrow">
								<a href="#" class="atss-preview-header-arrow-next"><i class="dashicons dashicons-arrow-right-alt2"></i></a>
							</div>
							<div class="atss-preview-header-column atss-preview-header-info">{{{ data.info }}}</div>
						</div>
						<div class="atss-preview-header-right">
							<a href="#" class="atss-preview-cancel-button button button-secondary">
								<?php esc_html_e( 'Cancel', 'athemes-starter-sites' ); ?>
							</a>
							<div class="atss-preview-header-actions">{{{ data.actions }}}</div>
						</div>
					</div>
					<iframe src="{{ data.preview }}" class="atss-preview-iframe"></iframe>
			</script>
		<?php
	}
	/**
	 * Import Template
	 */
	public function import_template() {
		?>
			<script type="text/html" id="tmpl-atss-import">
			
				<div class="atss-import-overlay atss-import-close-button"></div>

				<form class="atss-import-form">

					<input type="hidden" name="demo_id" value="{{ data.args.demoId }}" />

					<input type="hidden" name="start" data-action="atss_import_start" data-priority="20" data-log="<?php esc_html_e( 'Starting setup...', 'athemes-starter-sites' ); ?>" />

					<# var isStartFromFirstStep = ( ! data.args.quick ) ? ' atss-active' : ''; #>

					<div class="atss-import-step{{ isStartFromFirstStep }}">
						<div class="atss-import-title">
							<?php esc_html_e( 'Use one of these', 'athemes-starter-sites' ); ?>
							<div class="atss-import-close-button"><i class="dashicons dashicons-no-alt"></i></div>
						</div>
						<div class="atss-import-content">
							<div class="atss-import-content-block">
								<div class="atss-import-toggle atss-active">
									<div class="atss-import-toggle-title atss-import-toggle-button">
										<?php esc_html_e( 'Page Builder', 'athemes-starter-sites' ); ?>
										<i class="atss-import-toggle-icon dashicons dashicons-arrow-up-alt2"></i>
									</div>
									<div class="atss-import-toggle-content">
										<div class="atss-import-image-select atss-import-builder-select">
											<# _.each( data.builders, function( builder ) { #>
												<label class="atss-import-image-select-item">
													<# var builderChecked    = ( data.args.builder === builder ) ? ' checked="checked"' : ''; #>
													<# var builderPluginSlug = ( builder === 'gutenberg' ) ? 'athemes-blocks' : builder; #>
													<input type="radio" name="builder_type" value="{{ builder }}" data-builder-plugin="{{ builderPluginSlug }}" {{{ builderChecked }}} />
													<figure>
														<img src="{{ window.atss_localize.plugin_url }}v2/assets/img/builder-{{ builder }}.svg" />
													</figure>
													<div class="atss-import-image-select-name">{{ builder }}</div>
												</label>
											<# } ); #>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="atss-import-actions">
							<a href="#" class="atss-import-close-button button button-secondary"><?php esc_html_e( 'Close', 'athemes-starter-sites' ); ?></a>
							<a href="#" class="atss-import-next-button button button-primary"><?php esc_html_e( 'Next', 'athemes-starter-sites' ); ?></a>
						</div>
					</div>

					<# var isStartFromSecondStep = ( data.args.quick ) ? ' atss-active' : ''; #>

					<div class="atss-import-step{{ isStartFromSecondStep }}">
						<div class="atss-import-title">
							<?php esc_html_e( 'Choose your preferred type', 'athemes-starter-sites' ); ?>
							<div class="atss-import-close-button"><i class="dashicons dashicons-no-alt"></i></div>
						</div>
						<div class="atss-import-content">
							<# var isShowContentType = ( data.args.imported ) ? ' atss-hidden' : ''; #>
							<div class="atss-import-content-block atss-import-content-select{{ isShowContentType }}">
								<div class="atss-import-toggle atss-active">
									<div class="atss-import-toggle-title atss-import-toggle-button">
										<?php esc_html_e( 'Content Type', 'athemes-starter-sites' ); ?>
										<i class="atss-import-toggle-icon dashicons dashicons-arrow-up-alt2"></i>
									</div>
									<div class="atss-import-toggle-content">
										<div class="atss-import-image-select">
											<label class="atss-import-image-select-item">
												<input type="radio" name="content_type" value="entire-site" checked="checked" />
												<figure>
													<img src="{{ data.thumbnail }}" class="atss-content-type-entire-site" />
												</figure>
												<div class="atss-import-image-select-name"><?php esc_html_e( 'Entire Site', 'athemes-starter-sites' ); ?></div>
											</label>
											<label class="atss-import-image-select-item">
												<input type="radio" name="content_type" value="placeholder" />
												<figure>
													<img src="{{ window.atss_localize.plugin_url }}v2/assets/img/content-type-placeholder.svg" />
												</figure>
												<div class="atss-import-image-select-name"><?php esc_html_e( 'Placeholder', 'athemes-starter-sites' ); ?></div>
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="atss-import-content-block">
								<div class="atss-import-toggle atss-active">
									<div class="atss-import-toggle-title atss-import-toggle-button">
										<?php esc_html_e( 'Import Content', 'athemes-starter-sites' ); ?>
										<i class="atss-import-toggle-icon dashicons dashicons-arrow-up-alt2"></i>
									</div>
									<div class="atss-import-toggle-content">
										<div class="atss-import-checkboxes">
											<label>
												<input type="checkbox" data-action="atss_import_contents" class="atss-import-with-content-type" data-priority="40" data-log="<?php esc_html_e( 'Importing contents...', 'athemes-starter-sites' ); ?>" checked="checked" />
												<span><i></i></span>
												<?php esc_html_e( 'Content', 'athemes-starter-sites' ); ?>
											</label>
											<label>
												<input type="checkbox" data-action="atss_import_widgets" data-priority="41" data-log="<?php esc_html_e( 'Importing widgets...', 'athemes-starter-sites' ); ?>" checked="checked" />
												<span><i></i></span>
												<?php esc_html_e( 'Widgets', 'athemes-starter-sites' ); ?>
											</label>
											<label>
												<input type="checkbox" data-action="atss_import_customizer" data-priority="42" data-log="<?php esc_html_e( 'Importing customizer options...', 'athemes-starter-sites' ); ?>" checked="checked" />
												<span><i></i></span>
												<?php esc_html_e( 'Customizer', 'athemes-starter-sites' ); ?>
											</label>
										</div>
										<# if ( data.args.imported ) { #>
											<div class="atss-import-checkboxes atss-import-clean-checkboxes">
												<label>
													<input type="checkbox" data-action="atss_import_clean" class="atss-import-with-content-type" data-priority="10" data-log="<?php esc_html_e( 'Cleaning previous import data...', 'athemes-starter-sites' ); ?>" />
													<span><i></i></span>
													<?php esc_html_e( 'Clean Install', 'athemes-starter-sites' ); ?>
												</label>
												<div class="atss-import-clean-description"><?php esc_html_e( 'This option will remove the previous imported content and will perform a fresh and clean install.', 'athemes-starter-sites' ); ?></div>
											</div>
										<# } #>
									</div>
								</div>
							</div>
						</div>
						<div class="atss-import-actions">
							<# if ( data.args.quick ) { #>
								<a href="#" class="atss-import-close-button button button-secondary"><?php esc_html_e( 'Close', 'athemes-starter-sites' ); ?></a>
							<# } else { #>
								<a href="#" class="atss-import-prev-button button button-secondary"><?php esc_html_e( 'Prev', 'athemes-starter-sites' ); ?></a>
							<# } #>
							<# if ( window.atss_localize.settings.has_pro || data.type === 'free' ) { #>
								<a href="#" class="atss-import-next-button button button-primary"><?php esc_html_e( 'Next', 'athemes-starter-sites' ); ?></a>
							<# } #>
							<# if ( ! window.atss_localize.settings.has_pro && data.type === 'pro' ) { #>
								<a href="{{ window.atss_localize.settings.pro_link }}" target="_blank" class="atss-demo-pro-link-button button button-primary">{{ window.atss_localize.settings.pro_label }}</a>
							<# } #>
						</div>
					</div>

					<div class="atss-import-step">
						<div class="atss-import-title">
							<?php esc_html_e( 'Okay, just one last step...', 'athemes-starter-sites' ); ?>
							<div class="atss-import-close-button"><i class="dashicons dashicons-no-alt"></i></div>
						</div>
						<div class="atss-import-content">
							<div class="atss-import-content-block">
								<div class="atss-import-toggle atss-active">
									<div class="atss-import-toggle-title atss-import-toggle-button">
										<?php esc_html_e( 'Install Plugins', 'athemes-starter-sites' ); ?>
										<i class="atss-import-toggle-icon dashicons dashicons-arrow-up-alt2"></i>
									</div>
									<div class="atss-import-toggle-content">
										<div class="atss-import-checkboxes">
											<# _.each( data.builders, function( builder ) { #>
												<#
													var builderPluginSlug     = ( builder === 'gutenberg' ) ? 'athemes-blocks' : 'elementor';
													var builderPluginName     = ( builder === 'gutenberg' ) ? 'aThemes Blocks' : 'Elementor';
													var builderPluginChecked  = ( data.args.builder === builder ) ? ' checked="checked"' : '';
													var builderPluginActive   = ( data.args.builder !== builder ) ? ' atss-hidden' : '';
												#>
												<label class="atss-import-plugin-builder atss-import-plugin-{{ builderPluginSlug }} atss-import-plugin-required{{ builderPluginActive }}">
													<input type="checkbox" name="plugin" data-action="atss_import_plugin" data-priority="30" data-slug="{{ builderPluginSlug }}" data-path="{{ builderPluginSlug }}/{{ builderPluginSlug }}.php" data-log="<?php esc_html_e( 'Installing and activating', 'athemes-starter-sites' ); ?>: {{ builderPluginName }}" {{{ builderPluginChecked }}} />
													<span><i></i></span>
													{{ builderPluginName }}
												</label>
											<# } ); #>
											<# _.each( data.plugins, function( plugin ) { #>
												<# var isPluginRequired = ( plugin.required ) ? ' atss-import-plugin-required' : ''; #>
												<label class="atss-import-plugin-{{ plugin.slug }}{{ isPluginRequired }}">
													<input type="checkbox" name="plugin" data-action="atss_import_plugin" data-priority="30" data-slug="{{ plugin.slug }}" data-path="{{ plugin.path }}" data-log="<?php esc_html_e( 'Installing and activating', 'athemes-starter-sites' ); ?>: {{ plugin.name }}" checked="checked" />
													<span><i></i></span>
													{{{ plugin.name }}}
												</label>
											<# } ); #>
										</div>
									</div>
								</div>
							</div>
							<div class="atss-import-content-block">
								<div class="atss-import-content-block-title">
									<?php esc_html_e( 'Subscribe and Import', 'athemes-starter-sites' ); ?>
								</div>
								<div class="atss-import-subscribe">
									<div class="atss-import-subscribe-text"><?php esc_html_e( 'Subscribe to learn about new starter sites and features', 'athemes-starter-sites' ); ?></div>
									<label>
										<strong><?php esc_html_e( 'Email', 'athemes-starter-sites' ); ?></strong>
										<input type="email" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" class="atss-import-subscribe-field-email" />
									</label>
									<label>
										<input type="checkbox" value="yes" class="atss-import-subscribe-field-count-me" checked="checked" />
										<?php esc_html_e( 'Yes, count me in!', 'athemes-starter-sites' ); ?>
									</label>
									<small><?php esc_html_e( 'We do not spam, unsubscribe anytime.', 'athemes-starter-sites' ); ?></small>
								</div>
							</div>
						</div>
						<div class="atss-import-actions atss-import-actions-column">
							<a href="#" class="atss-import-start-button button button-primary" data-subscribe="yes"><?php esc_html_e( 'Subscribe and Start Importing', 'athemes-starter-sites' ); ?></a>
							<a href="#" class="atss-import-start-button button button-secondary"><?php esc_html_e( 'Skip, Start Importing', 'athemes-starter-sites' ); ?></a>
						</div>
					</div>

					<div class="atss-import-step">
						<div class="atss-import-title">
							<?php esc_html_e( 'We are building your website', 'athemes-starter-sites' ); ?>
						</div>
						<div class="atss-import-content">
							<div class="atss-import-content-block">
								<?php esc_html_e( 'Please be patient and don’t refresh this page, the import process may take a while, this also depends on your server.', 'athemes-starter-sites' ); ?>
								<div class="atss-import-progress">
									<div class="atss-import-progress-info">
										<div class="atss-import-progress-label"></div>
										<div class="atss-import-progress-sublabel">0%</div>
									</div>
									<div class="atss-import-progress-bar">
										<div class="atss-import-progress-indicator" style="--atss-indicator: 0%;"></div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="atss-import-step atss-import-step-error">
						<div class="atss-import-error-content">
							<div class="atss-import-error-image">
								<figure>
									<img src="<?php echo esc_url( ATSS_URL . 'v2/assets/img/error.svg' ); ?>" />
								</figure>
							</div>
							<div class="atss-import-error-title">
								<?php esc_html_e( 'Sorry, something went wrong', 'athemes-starter-sites' ); ?>
							</div>
							<div class="atss-import-error-box">
								<div class="atss-import-error-message">
									<strong><?php esc_html_e( 'What went wrong', 'athemes-starter-sites' ); ?></strong>
									<?php esc_html_e( 'Please be patient and don’t refresh this page.', 'athemes-starter-sites' ); ?>
								</div>
								<div class="atss-import-error-message">
									<strong><small><?php esc_html_e( 'More technical information from console', 'athemes-starter-sites' ); ?></small></strong>
									<div class="atss-import-error-log"></div>
								</div>
							</div>
							<a href="#" class="atss-import-open-button button button-primary" data-demo-id="{{ data.args.demoId }}"><?php esc_html_e( 'Click here and try again', 'athemes-starter-sites' ); ?></a>
						</div>
					</div>

					<div class="atss-import-step atss-import-step-finish">
						<div class="atss-import-finish-content">
							<div class="atss-import-finish-title">
								<?php esc_html_e( 'Congratulations!', 'athemes-starter-sites' ); ?>
							</div>
							<?php esc_html_e( 'Your website is ready. Go ahead, customize the text, images and design to make it yours!', 'athemes-starter-sites' ); ?>
							<div class="atss-import-finish-actions">
								<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" target="_blank" class="button button-secondary"><?php esc_html_e( 'Customize', 'athemes-starter-sites' ); ?></a>
								<a href="<?php echo esc_url( site_url( '/' ) ); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'View Site', 'athemes-starter-sites' ); ?></a>
							</div>
							<div class="atss-import-finish-tweet">
								<p class="atss-import-finish-tweet-text"></p>
								<a href="#" target="_blank" class="atss-import-finish-tweet-button">
									<?php esc_html_e( 'Click to Tweet', 'athemes-starter-sites' ); ?>
									<i class="dashicons dashicons-twitter"></i>
								</a>
							</div>
						</div>
					</div>

					<input type="hidden" name="finish" data-action="atss_import_finish" data-priority="50" data-log="<?php esc_html_e( 'Finishing setup...', 'athemes-starter-sites' ); ?>"/>

				</form>

			</script>
		<?php
	}

	/**
	 * Is template of aThemes
	 */
	public function is_athemes_template() {

		$theme = wp_get_theme();

		if ( $theme->parent() ) {
			$theme = $theme->parent();
		}

		$themes = array(
			'Botiga',
		);

		return in_array( $theme->name, $themes );

	}

	/**
	 * Display a notification.
	 */
	public function html_notice() {

		if ( ! $this->is_athemes_template() && ! get_transient( 'atss_no_active_theme' ) ) {
			?>
			<div class="atss-notice notice notice-warning is-dismissible">
				<p>
				<?php
					// Translators: Link.
					echo wp_kses( sprintf( __( 'aThemes Sites Import (plugin) requires an %1$s theme to be installed and activated.', 'athemes-starter-sites' ), '<a href="https://athemes.com/" target="_blank">' . __( 'aThemes', 'athemes-starter-sites' ) . '</a>' ), 'post' );
				?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Purified from the database information about notification.
	 */
	public function reset_notices() {
		delete_transient( 'atss_no_active_theme' );
	}

  /**
   * Dismissed handler
   */
  public function ajax_dismissed_handler() {

    check_ajax_referer( 'nonce', 'nonce' );
    set_transient( 'atss_no_active_theme', true, 90 * DAY_IN_SECONDS );
    wp_send_json_success();

  }

}

new ATSS_Demos_Page();
