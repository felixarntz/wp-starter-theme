<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

final class Customizer {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function run() {
		add_action( 'wpcd', array( $this, 'add_customize_components' ), 10, 1 );
		add_action( 'customize_register', array( $this, 'customize_register' ), 10, 1 );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
	}

	public function add_customize_components( $wpcd ) {

	}

	public function customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial( 'blogname', array(
				'selector'				=> '.site-title a',
				'container_inclusive'	=> false,
				'render_callback'		=> array( $this, 'partial_blogname' ),
			) );
			$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
				'selector'				=> '.site-description',
				'container_inclusive'	=> false,
				'render_callback'		=> array( $this, 'partial_blogdescription' ),
			) );
		}
	}

	public function customize_preview_init() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version = Theme::get_info( 'Version' );

		wp_enqueue_script( 'wp-starter-theme-customize-preview', Util\Path::get_url( 'assets/dist/js/customize-preview' . $min . '.js' ), array( 'jquery', 'customize-preview' ), $version, true );
	}

	public function partial_blogname() {
		bloginfo( 'name' );
	}

	public function partial_blogdescription() {
		bloginfo( 'description' );
	}
}
