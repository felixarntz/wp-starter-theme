<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

/**
 * Class to improve shortcode handling.
 *
 * @since 1.0.0
 */
final class Shortcodes {
	/**
	 * Array of shortcodes to execute early.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var array
	 */
	private static $early_shortcodes = array();

	/**
	 * Adds a shortcode.
	 *
	 * This method can be called instead of add_shortcode() to support early shortcode parsing.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string   $tag   Shortcode tag.
	 * @param callable $func  Shortcode callback.
	 * @param bool     $early Optional. Whether the shortcode should be parsed early. Default false.
	 */
	public static function add_shortcode( $tag, $func, $early = false ) {
		if ( $early ) {
			self::$early_shortcodes[ $tag ] = $func;
		}

		\add_shortcode( $tag, $func );
	}

	/**
	 * Adds general filters to support early shortcode parsing.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		add_filter( 'the_content', array( __CLASS__, 'do_early_shortcodes' ), 9 );
		add_filter( 'the_content', array( __CLASS__, 'fix_early_shortcodes' ), 12 );
	}

	/**
	 * Parses early shortcodes.
	 *
	 * This method is used as callback and should not be called directly.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @internal
	 *
	 * @param string $content Post content.
	 * @return string The modified content.
	 */
	public static function do_early_shortcodes( $content ) {
		global $shortcode_tags;

		if ( 0 === count( self::$early_shortcodes ) ) {
			return $content;
		}

		$original_shortcode_tags = $shortcode_tags;
		$shortcode_tags = self::$early_shortcodes;

		$content = do_shortcode( $content );

		$shortcode_tags = array_diff_key( $original_shortcode_tags, self::$early_shortcodes );

		return $content;
	}

	/**
	 * Recreates the original shortcodes array.
	 *
	 * This method is used as callback and should not be called directly.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @internal
	 *
	 * @param string $content Post content.
	 * @return string Post content.
	 */
	public static function fix_early_shortcodes( $content ) {
		global $shortcode_tags;

		if ( 0 === count( self::$early_shortcodes ) ) {
			return $content;
		}

		$shortcode_tags = array_merge( $shortcode_tags, self::$early_shortcodes );

		return $content;
	}
}
