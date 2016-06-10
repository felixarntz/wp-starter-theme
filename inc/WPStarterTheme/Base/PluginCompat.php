<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

final class PluginCompat {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function run() {
		add_filter( 'give_display_checkout_button', array( $this, 'give_button' ) );
		add_filter( 'give_checkout_button_purchase', array( $this, 'give_button' ) );
	}

	public function give_button( $output ) {
		return str_replace( array( 'give-btn give-btn-reveal', 'give-btn"' ), array( 'btn btn-primary give-btn-reveal', 'btn btn-primary"' ), $output );
	}
}
