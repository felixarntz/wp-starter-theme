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
		/* Jetpack */
		add_action( 'after_setup_theme', array( $this, 'jetpack_theme_support' ) );

		/* Frontkit */
		add_action( 'after_setup_theme', array( $this, 'frontkit_theme_support' ) );

		/* Easy Digital Downloads */
		add_filter( 'shortcode_atts_purchase_link', array( $this, 'edd_button_args' ), 10, 3 );
		add_filter( 'edd_purchase_link_defaults', array( $this, 'edd_button_args' ) );
		add_filter( 'edd_checkout_button_next',     array( $this, 'edd_button' ) );
		add_filter( 'edd_checkout_button_purchase', array( $this, 'edd_button' ) );

		/* Give WP */
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
	 * Adjusts purchase button classes for Easy Digital Downloads.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 *
	 * @param array $args     Purchase link or download shortcode arguments.
	 * @param array $defaults Optional. Download shortcode defaults.
	 * @param array $atts     Optional. Download shortcode attributes.
	 * @return array The modified arguments.
	 */
	public function edd_button_args( $args, $defaults = null, $atts = null ) {
		if ( is_array( $defaults ) && is_array( $atts ) ) {
			if ( ! isset( $atts['class'] ) ) {
				$args['class'] = 'edd-submit btn btn-primary';
			}
		} else {
			$args['class'] = 'edd-submit btn btn-primary';
		}

		return $args;
	}

	/**
	 * Adjusts checkout button classes for Easy Digital Downloads.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 *
	 * @param string $output Button output.
	 * @return string Modified button output.
	 */
	public function edd_button( $output ) {
		return str_replace( 'class="edd-submit ', 'class="edd-submit btn btn-primary ', $output );
	}

	/**
	 * Adjusts button classes for Give WP.
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
