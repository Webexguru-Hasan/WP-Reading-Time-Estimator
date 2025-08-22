<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
exit;
}
/**
 * Admin area: menu, assets, and settings page bootstrap.
 */
class WPRTE_Admin {
	/**
	 * Register admin hooks.
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	/**
	 * Register the plugin menu with a custom SVG icon.
	 */
	public function register_menu() {
    $capability = 'manage_options';
    $slug       = 'wprte-settings';

    // Inline SVG icon
   $icon_url = plugins_url( 'assets/admin-icon.svg', dirname( __FILE__ ) );

    add_menu_page(
        __( 'Reading Time', 'wp-reading-time-estimator' ),
        __( 'Reading Time', 'wp-reading-time-estimator' ),
        $capability,
        $slug,
        array( $this, 'render_settings_page' ),
        $icon_url,
        6
    );
}


	/**
	 * Render the settings page (delegates to a separate file for Settings API in Step 2).
	 */
	public function render_settings_page() {
    require_once WPRTE_PATH . 'admin/settings-page.php';

    if ( function_exists( 'wprte_render_settings_page' ) ) {
        wprte_render_settings_page();
    }
}

}