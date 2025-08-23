<?php
/*
 * Plugin Name:       WP Reading Time Estimator
 * Plugin URI:        https://wordpress.org/plugins/wp-reading-time-estimator/
 * Description:       Display an estimated reading time for your posts and pages. Simple, lightweight, and customizable.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            Hasan Wazid
 * Author URI:        https://hasan-wazid-portfolio.vercel.app/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/Webexguru-Hasan
 * Text Domain:       wp-reading-time-estimator
 * Domain Path:       /languages
 * 
 *  @package WP_Reading_Time_Estimator
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
exit;
}


// Define plugin constants.
if ( ! defined( 'WPRTE_VERSION' ) ) {
define( 'WPRTE_VERSION', '1.0.0' );
}
if ( ! defined( 'WPRTE_FILE' ) ) {
define( 'WPRTE_FILE', __FILE__ );
}
if ( ! defined( 'WPRTE_PATH' ) ) {
define( 'WPRTE_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WPRTE_URL' ) ) {
define( 'WPRTE_URL', plugin_dir_url( __FILE__ ) );
}


// Simple autoloader for our classes (PSR-4-like with prefix "WPRTE_").
spl_autoload_register( function ( $class ) {
if ( 0 !== strpos( $class, 'WPRTE_' ) ) {
return;
}


$map = array(
'WPRTE_Loader' => 'includes/class-wprte-loader.php',
'WPRTE_Activator' => 'includes/class-wprte-activator.php',
'WPRTE_Deactivator' => 'includes/class-wprte-deactivator.php',
'WPRTE_Uninstaller' => 'includes/class-wprte-uninstaller.php',
'WPRTE_Admin' => 'admin/class-wprte-admin.php',
'WPRTE_Public' => 'public/class-wprte-public.php',
);


if ( isset( $map[ $class ] ) ) {
require_once WPRTE_PATH . $map[ $class ];
}
} );

/**
* Activation / Deactivation / Uninstall hooks
*/
register_activation_hook( __FILE__, array( 'WPRTE_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WPRTE_Deactivator', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'WPRTE_Uninstaller', 'uninstall' ) );

// Load Admin Settings Page
if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/settings-page.php';
}

require_once plugin_dir_path( __FILE__ ) . 'includes/frontend.php';


/**
* Load plugin textdomain for translations
*/
function wprte_load_textdomain() {
load_plugin_textdomain( 'wp-reading-time-estimator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wprte_load_textdomain' );


/**
* Kick off the plugin
*/
function wprte_run() {
$loader = new WPRTE_Loader();
$loader->init();
}
add_action( 'plugins_loaded', 'wprte_run', 20 );

?>