<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

final class Assets {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function run() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function enqueue() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version = Theme::instance()->get_info( 'Version' );
		$dependencies = array( 'jquery' );

		wp_enqueue_style( 'wp-starter-theme', Util\Path::get_url( 'assets/dist/css/app' . $min . '.css' ), array(), $version, 'all' );
		wp_enqueue_script( 'wp-starter-theme', Util\Path::get_url( 'assets/dist/js/app' . $min . '.js' ), $dependencies, $version, true );

		if ( WP_DEBUG ) {
			wp_enqueue_script( 'livereload', untrailingslashit( home_url() ) . ':35729/livereload.js?snipver=1', array(), false, true );
		}
	}
}
