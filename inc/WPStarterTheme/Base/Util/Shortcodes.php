<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

final class Shortcodes {
	private static $early_shortcodes = array();

	public static function add_shortcode( $tag, $func, $early = false ) {
		if ( $early ) {
			self::$early_shortcodes[ $tag ] = $func;
		}

		\add_shortcode( $tag, $func );
	}

	public static function init() {
		add_filter( 'the_content', array( __CLASS__, 'do_early_shortcodes' ), 9 );
		add_filter( 'the_content', array( __CLASS__, 'fix_early_shortcodes' ), 12 );
	}

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

	public static function fix_early_shortcodes( $content ) {
		global $shortcode_tags;

		if ( 0 === count( self::$early_shortcodes ) ) {
			return $content;
		}

		$shortcode_tags = array_merge( $shortcode_tags, self::$early_shortcodes );

		return $content;
	}
}
