<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

/**
 * Class to handle paths and URLs to theme files.
 *
 * @since 1.0.0
 */
final class Path {
	/**
	 * Returns the full path to a file inside the theme.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $sub_path Relative path from the theme root directory.
	 * @return string The full path.
	 */
	public static function get_path( $sub_path = '' ) {
		return self::join_paths( get_template_directory(), $sub_path );
	}

	/**
	 * Returns the full URL to a file inside the theme.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $sub_path Relative path from the theme root directory.
	 * @return string The full URL.
	 */
	public static function get_url( $sub_path = '' ) {
		return self::join_paths( get_template_directory_uri(), $sub_path );
	}

	/**
	 * Joins two path parts.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param string $base_path The first path part.
	 * @param string $sub_path  The second path part.
	 * @return string The joined path result.
	 */
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
