<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

final class Path {
	public static function get_path( $sub_path = '' ) {
		return self::join_paths( get_template_directory(), $sub_path );
	}

	public static function get_url( $sub_path = '' ) {
		return self::join_paths( get_template_directory_uri(), $sub_path );
	}

	private static function join_paths( $base_path, $sub_path = '' ) {
		if ( empty( $sub_path ) ) {
			return $base_path;
		} elseif ( 0 === strpos( $sub_path, $base_path ) ) {
			return $sub_path;
		} else {
			return $base_path . '/' . ltrim( $sub_path, '/' );
		}
	}
}
