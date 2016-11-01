<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

/**
 * Class to handle customizer functionality.
 *
 * @since 1.0.0
 */
final class Customizer {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Adds the necessary hooks to initialize customizer functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function run() {
		add_action( 'wpcd', array( $this, 'add_customize_components' ), 10, 1 );
		add_action( 'customize_register', array( $this, 'customize_register' ), 10, 1 );
		add_filter( 'wpcd_custom_callback_function_scripts', array( $this, 'customize_script' ), 10, 1 );
		add_action( 'customize_preview_init', array( $this, 'customize_localize_script' ), 100, 1 );
	}

	/**
	 * Adds customizer components via the Customizer Definitely library.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 *
	 * @param WPCD\App $wpcd The Customizer Definitely instance.
	 */
	public function add_customize_components( $wpcd ) {

	}

	/**
	 * Registers and adjusts several customizer fields.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 *
	 * @param WP_Customize_Manager $wp_customize The customize manager instance.
	 */
	public function customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial( 'blogname', array(
				'selector'				=> '.site-title a',
				'container_inclusive'	=> false,
				'render_callback'		=> array( Partials::instance(), 'render_blogname' ),
			) );
			$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
				'selector'				=> '.site-description',
				'container_inclusive'	=> false,
				'render_callback'		=> array( Partials::instance(), 'render_blogdescription' ),
			) );
		}
	}

	/**
	 * Registers the additional customizer script via Customizer Definitely.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 *
	 * @param array $scripts Array of script slugs and their URLs.
	 * @return array The modified array.
	 */
	public function customize_script( $scripts ) {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$scripts['wp-starter-theme-customize-preview'] = Util\Path::get_url( 'assets/dist/js/customize-preview' . $min . '.js' );

		return $scripts;
	}

	/**
	 * Adds script variables to the customizer script.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 */
	public function customize_localize_script() {
		wp_localize_script( 'wp-starter-theme-customize-preview', lcfirst( 'WPStarterTheme' ), array(
			'nonces'	=> AJAX::instance()->get_nonces(),
		) );
	}
}
