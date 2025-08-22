<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
exit;
}
/**
 * Handles plugin uninstall cleanup.
 */
class WPRTE_Uninstaller {
	public static function uninstall() {
		// Remove options and meta/transients created by the plugin.
		delete_option( 'wprte_settings' );

		// If we cache reading time in post meta/transients, clean them here (to be added in a later step).
		// Example: delete_post_meta_by_key( '_wprte_reading_time' );
	}
}