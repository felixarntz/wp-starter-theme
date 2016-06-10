<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

final class Partials {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
	}

	public function render_blogname() {
		bloginfo( 'name' );
	}

	public function render_blogdescription() {
		bloginfo( 'description' );
	}
}
