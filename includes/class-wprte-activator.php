<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
exit;
}
/**
 * Handles plugin activation tasks.
 */
class WPRTE_Activator {
	public static function activate() {
		// Set default options on first install.
		$defaults = array(
			'post_types'          => array( 'post' ),
			'reading_speed'       => 200, // words per minute default.
			'extra_seconds_image' => 10,
			'auto_inject'         => 'none', // 'before' | 'after' | 'none'.
			'output_format'       => __( '%s min read', 'wp-reading-time-estimator' ),
			'enable_schema'       => false,
		);

		if ( ! get_option( 'wprte_settings' ) ) {
			add_option( 'wprte_settings', $defaults );
		}
	}
}