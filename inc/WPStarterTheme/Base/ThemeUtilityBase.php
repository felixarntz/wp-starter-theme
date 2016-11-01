<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

/**
 * Theme utility base class.
 *
 * @since 1.0.0
 */
abstract class ThemeUtilityBase {
	/**
	 * The theme instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var WPStarterTheme\Base\Theme
	 */
	protected $theme;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WPStarterTheme\Base\Theme $theme The theme instance.
	 */
	public function __construct( $theme ) {
		$this->theme = $theme;
	}

	/**
	 * Adds the necessary hooks.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public abstract function run();
}
