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
		$dependencies = array( 'jquery', 'fancybox' );

		wp_enqueue_style( 'fancybox', Util\Path::get_url( 'assets/vendor/fancybox/source/jquery.fancybox.css' ), array(), '2.1.5' );
		wp_enqueue_script( 'fancybox', Util\Path::get_url( 'assets/vendor/fancybox/source/jquery.fancybox.pack.js' ), array( 'jquery' ), '2.1.5', true );

		wp_enqueue_style( 'wp-starter-theme', Util\Path::get_url( 'assets/dist/css/app' . $min . '.css' ), array(), $version, 'all' );
		wp_enqueue_script( 'wp-starter-theme', Util\Path::get_url( 'assets/dist/js/app' . $min . '.js' ), $dependencies, $version, true );
		wp_localize_script( 'wp-starter-theme', 'wp_theme', $this->get_script_vars() );

		if ( is_singular() ) {
			wp_enqueue_script( 'comment-reply' );
		}

		if ( WP_DEBUG ) {
			wp_enqueue_script( 'livereload', untrailingslashit( home_url() ) . ':35729/livereload.js?snipver=1', array(), false, true );
		}
	}

	private function get_script_vars() {
		$theme = Theme::instance();

		$vars = array(
			'name'			=> $theme->get_info( 'Name' ),
			'description'	=> $theme->get_info( 'Description' ),
			'version'		=> $theme->get_info( 'Version' ),
			'ajax'			=> array(
				'url'			=> admin_url( 'admin-ajax.php' ),
				'nonce'			=> wp_create_nonce( 'wp-starter-theme' ),
			),
			'settings'		=> array(
				'init_tooltips'	=> false,
				'init_popovers'	=> false,
				'init_fancybox'	=> true,
				'wrap_embeds'	=> true,
			),
			'i18n'			=> array(),
		);

		return $vars;
	}
}
