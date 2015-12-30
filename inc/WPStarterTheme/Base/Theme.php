<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

final class Theme {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function run() {
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
	}
}

require_once dirname( dirname( __FILE__ ) ) . '/functions.php';
