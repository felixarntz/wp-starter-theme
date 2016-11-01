<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

/**
 * Class to handle compatibility with several plugins.
 *
 * @since 1.0.0
 */
final class PluginCompat extends ThemeUtilityBase {
	/**
	 * Adds the necessary hooks to initialize plugin compatibility functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function run() {
		add_action( 'after_setup_theme', array( $this, 'jetpack_theme_support' ) );
		add_action( 'after_setup_theme', array( $this, 'frontkit_theme_support' ) );

		add_filter( 'give_display_checkout_button', array( $this, 'give_button' ) );
		add_filter( 'give_checkout_button_purchase', array( $this, 'give_button' ) );
	}

	/**
	 * Adds theme support for Jetpack functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 */
	public function jetpack_theme_support() {
		add_theme_support( 'infinite-scroll', array(
			'container'      => 'posts-list',
			'footer'         => false,
			'footer_widgets' => false,
			'wrapper'        => false,
			'render'         => array( $this->theme->partials(), 'render_loop' ),
		) );
	}

	/**
	 * Adds theme support for Frontkit functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 */
	public function frontkit_theme_support() {
		add_theme_support( 'frontkit', array(
			'title'   => '.single-post .post-title',
			'content' => '.single-post .post-content',
		) );
	}

	/**
	 * Adjusts button classes for the Give WP plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 *
	 * @param string $output Button output.
	 * @return string Modified button output.
	 */
	public function give_button( $output ) {
		return str_replace( array( 'give-btn give-btn-reveal', 'give-btn"' ), array( 'btn btn-primary give-btn-reveal', 'btn btn-primary"' ), $output );
	}
}
