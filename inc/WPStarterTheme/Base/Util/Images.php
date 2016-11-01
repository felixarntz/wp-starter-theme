<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

/**
 * Class to improve image size handling.
 *
 * @since 1.0.0
 */
final class Images {
	/**
	 * Contains the image sizes added with the class.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var array
	 */
	private static $selectable_sizes = array();

	/**
	 * Adds an image size.
	 *
	 * This method can be called instead of add_image_size() to support selection of the image size in the media modal.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string      $name       Name for the image size.
	 * @param int         $width      Width for the image size.
	 * @param int         $height     Height for the image size.
	 * @param bool        $crop       Optional. Whether to crop images to the exact size. Default false.
	 * @param string|bool $selectable Optional. Whether the image size should be selectable. Default false.
	 */
	public static function add_size( $name, $width, $height, $crop = false, $selectable = false ) {
		\add_image_size( $name, $width, $height, $crop );

		if ( $selectable ) {
			if ( ! is_string( $selectable ) ) {
				$selectable = ucwords( str_replace( '-', ' ', $name ) );
			}
			self::$selectable_sizes[ $name ] = $selectable;
		}
	}

	/**
	 * Adds general filters to support selection of additional image sizes.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		add_action( 'image_size_names_choose', array( __CLASS__, 'register_selectable_sizes' ) );
	}

	/**
	 * Adds further image sizes to the selectable sizes in the media modal.
	 *
	 * This method is used as callback and should not be called directly.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @internal
	 *
	 * @param array $sizes Image sizes.
	 * @return array Modified image sizes.
	 */
	public static function register_selectable_sizes( $sizes ) {
		return array_merge( $sizes, self::$selectable_sizes );
	}
}
