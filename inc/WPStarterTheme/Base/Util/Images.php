<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

final class Images {
	private static $selectable_sizes = array();

	public static function add_size( $name, $width, $height, $crop = false, $selectable = false ) {
		\add_image_size( $name, $width, $height, $crop );

		if ( $selectable ) {
			if ( ! is_string( $selectable ) ) {
				$selectable = ucwords( str_replace( '-', ' ', $name ) );
			}
			self::$selectable_sizes[ $name ] = $selectable;
		}
	}

	public static function init() {
		add_action( 'image_size_names_choose', array( __CLASS__, 'register_selectable_sizes' ) );
	}

	public static function register_selectable_sizes( $sizes ) {
		return array_merge( $sizes, self::$selectable_sizes );
	}
}
