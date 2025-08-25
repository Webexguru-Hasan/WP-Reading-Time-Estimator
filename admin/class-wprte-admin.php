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
		$parent_slug = 'wprte-dashboard';
		$settings_slug = 'wprte-settings';

		// Inline SVG icon
		$icon_url = plugins_url( 'assets/admin-icon.svg', dirname( __FILE__ ) );

		// Parent menu (Dashboard)
		add_menu_page(
			__( 'Reading Time', 'wp-reading-time-estimator' ),
			__( 'Reading Time', 'wp-reading-time-estimator' ),
			$capability,
			$parent_slug,
			array( $this, 'render_dashboard_page' ),
			$icon_url,
			80
		);

		// Submenu → Settings
		add_submenu_page(
			$parent_slug,
			__( 'Settings', 'wp-reading-time-estimator' ),
			__( 'Settings', 'wp-reading-time-estimator' ),
			$capability,
			$settings_slug,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render Dashboard Page
	 */
	public function render_dashboard_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Reading Time Dashboard', 'wp-reading-time-estimator' ); ?></h1>
			<p><?php esc_html_e( 'Welcome to the Reading Time Estimator plugin!', 'wp-reading-time-estimator' ); ?></p>
			<p><?php esc_html_e( 'Use the Settings page to configure how reading time is displayed.', 'wp-reading-time-estimator' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render the settings page
	 */
	public function render_settings_page() {
		require_once WPRTE_PATH . 'admin/settings-page.php';

		if ( function_exists( 'wprte_render_settings_page' ) ) {
			wprte_render_settings_page();
		}
	}
}
