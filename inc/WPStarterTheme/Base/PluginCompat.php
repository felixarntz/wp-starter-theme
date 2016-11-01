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
		add_filter( 'give_display_checkout_button', array( $this, 'give_button' ) );
		add_filter( 'give_checkout_button_purchase', array( $this, 'give_button' ) );
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
