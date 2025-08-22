<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
exit;
}

/**
 * Handles plugin deactivation tasks.
 */
class WPRTE_Deactivator {
	public static function deactivate() {
		// No data removed on deactivate. Clean cron or caches if any in future.
	}
}