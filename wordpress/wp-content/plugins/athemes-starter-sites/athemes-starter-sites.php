<?php
/**
 * Plugin Name:       aThemes Starter Sites
 * Description:       Starter Sites for Sydney, Botiga and Airi
 * Version:           1.0.33
 * Author:            aThemes
 * Author URI:        https://athemes.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       athemes-starter-sites
 * Domain Path:       /languages
 *
 * @link              https://athemes.com
 * @package           Athemes Starter Sites
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Variables
 */
define( 'ATSS_URL', plugin_dir_url( __FILE__ ) );
define( 'ATSS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
$theme = wp_get_theme();
$theme = ( get_template_directory() !== get_stylesheet_directory() && $theme->parent() ) ? $theme->parent() : $theme;

/**
 * Boot version 2, If theme is "Botiga" and version >= 1.2.3+
 */
if ( $theme->name === 'Botiga' && version_compare( $theme->version, '1.2.3', '>=' ) ) {
  require_once ATSS_PATH . '/v2/classes/class-core.php';
} else {
  require_once ATSS_PATH . '/core/class-core.php';
}

/**
 * Plugin Activation.
 *
 * @param bool $networkwide The networkwide.
 */
function atss_plugin_activation( $networkwide ) {
	do_action( 'atss_plugin_activation', $networkwide );
}
register_activation_hook( __FILE__, 'atss_plugin_activation' );

/**
 * Plugin Deactivation.
 *
 * @param bool $networkwide The networkwide.
 */
function atss_plugin_deactivation( $networkwide ) {
	do_action( 'atss_plugin_deactivation', $networkwide );
}
register_deactivation_hook( __FILE__, 'atss_plugin_deactivation' );

/**
 * Language
 */
load_plugin_textdomain( 'athemes-starter-sites', false, plugin_basename( ATSS_PATH ) . '/languages' );