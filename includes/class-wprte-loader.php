<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
exit;
}


/**
 * Main loader that wires Admin and Public layers.
 */


class WPRTE_Loader {
	/**
	 * Initialize plugin parts.
	 */
	public function init() {
		// Admin side.
		if ( is_admin() ) {
			$admin = new WPRTE_Admin();
			$admin->hooks();
		}

		// Public side.
		$public = new WPRTE_Public();
		$public->hooks();
	}
}