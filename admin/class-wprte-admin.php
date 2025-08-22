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

		// Minimal inline SVG (book/clock) encoded as data URI. Replace with your own if desired.
		$svg = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 3h9a3 3 0 013 3v11a4 4 0 00-4-4H7a2 2 0 00-2 2V3z"/><path d="M7 17h6a2 2 0 012 2v2H7a2 2 0 110-4z"/><circle cx="18" cy="7" r="3"/><path d="M18 5v2l1.5 1.5"/></svg>' );

		add_menu_page(
			__( 'Reading Time', 'wp-reading-time-estimator' ),
			__( 'Reading Time', 'wp-reading-time-estimator' ),
			$capability,
			$slug,
			array( $this, 'render_settings_page' ),
			$svg,
			6
		);
	}

	/**
	 * Render the settings page (delegates to a separate file for Settings API in Step 2).
	 */
	public function render_settings_page() {
		require_once WPRTE_PATH . 'admin/settings-page.php'; // This file will be implemented in Step 2.
	}
}