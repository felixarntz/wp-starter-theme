<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

/**
 * Walker class to render nav menus in a Bootstrap-compatible way.
 *
 * @since 1.0.0
 */
final class BootstrapNavMenu extends \Walker_Nav_Menu {
	/**
	 * CSS class to add to all menu item list elements.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var string
	 */
	private static $li_class = '';

	/**
	 * CSS class to add to all menu item links.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var string
	 */
	private static $a_class = '';

	/**
	 * Temporary holder for current link attributes.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var array
	 */
	private static $a_atts_override = array();

	/**
	 * Temporary holder for the current dropdown label ID.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $current_label_id = '';

	/**
	 * Temporary holder for the current dropdown ID.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $current_dropdown_id = '';

	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = $this->get_indent( $depth, $args );
		$newline = $this->get_newline( $depth, $args );

		$output .= "{$newline}{$indent}<div id=\"{$this->current_dropdown_id}\" class=\"dropdown-menu\" aria-labelledby=\"{$this->current_label_id}\">{$newline}";

		$this->current_label_id    = '';
		$this->current_dropdown_id = '';
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = $this->get_indent( $depth, $args );
		$newline = $this->get_newline( $depth, $args );

		$output .= "$indent</div>{$newline}";
	}

	/**
	 * Starts the element output.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		if ( 0 === $depth && ( 0 === strcasecmp( $item->attr_title, 'divider' ) || 0 === strcasecmp( $item->title, 'divider' ) ) ) {
			$output .= $this->get_indent( $depth, $args ) . '<li role="presentation" class="divider">';
		} elseif ( 0 === $depth && 0 === strcasecmp( $item->attr_title, 'dropdown-header' ) ) {
			$output .= $this->get_indent( $depth, $args ) . '<li role="presentation" class="dropdown-header">' . esc_html( $item->title );
		} elseif ( 0 === $depth && 0 === strcasecmp( $item->attr_title, 'disabled' ) ) {
			$output .= $this->get_indent( $depth, $args ) . '<li role="presentation" class="disabled"><a href="#">' . esc_html( $item->title ) . '</a>';
		} else {
			$html = '';

			if ( $item->is_dropdown && 0 === $depth ) {
				$slug = sanitize_title( $item->title );

				$this->current_label_id    = $slug . '-dropdown-label';
				$this->current_dropdown_id = $slug . '-dropdown-menu';

				self::$a_atts_override = array(
					'id'            => $this->current_label_id,
					'class'         => self::$a_class . ' dropdown-toggle',
					'data-toggle'   => 'dropdown',
					'aria-controls' => $this->current_dropdown_id,
					'aria-haspopup' => 'true',
					'aria-expanded' => 'false',
				);
			} elseif ( $depth > 0 ) {
				self::$a_atts_override = array(
					'class' => 'dropdown-item',
				);
			}

			parent::start_el( $html, $item, $depth, $args, $id );

			if ( $depth > 0 ) {
				$html = preg_replace( '/<li(.*)>/iU', '', $html );
			}

			$output .= $html;
		}
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param WP_Post  $item   Menu item data object. Not used.
	 * @param int      $depth  Depth of menu item. Not Used.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		if ( $depth > 0 ) {
			$output .= $this->get_newline( $depth, $args );
		} else {
			parent::end_el( $output, $item, $depth, $args );
		}
	}

	/**
	 * Traverses elements to create list from elements.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WP_Post $item              Menu item data object.
	 * @param array   $children_elements List of elements to continue traversing.
	 * @param int     $max_depth         Max depth to traverse.
	 * @param int     $depth             Depth of current element.
	 * @param array   $args              An array of arguments.
	 * @param string  $output            Passed by reference. Used to append additional content.
	 */
	public function display_element( $item, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		if ( ! $item ) {
			return;
		}

		$id_field = $this->db_fields['id'];
		$id       = $item->$id_field;

		$item->is_dropdown = ! empty( $children_elements[ $id ] ) && ( $depth + 1 < $max_depth || 0 === $max_depth );

		if ( $item->is_dropdown ) {
			$item->classes[] = 'dropdown';
		}

		parent::display_element( $item, $children_elements, $max_depth, $depth, $args, $output );
	}

	/**
	 * Returns the indentation for a given depth.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param int      $depth Depth of menu item.
	 * @param stdClass $args  An object of wp_nav_menu() arguments.
	 * @return string The indentation string.
	 */
	private function get_indent( $depth, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			return '';
		}

		return str_repeat( "\t", $depth );
	}

	/**
	 * Returns the newline character for a given depth.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param int      $depth Depth of menu item.
	 * @param stdClass $args  An object of wp_nav_menu() arguments.
	 * @return string The newline string.
	 */
	private function get_newline( $depth, $args = array() ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			return '';
		}

		return "\n";
	}

	/**
	 * Renders a specific nav menu for given arguments.
	 *
	 * This method should be called instead of wp_nav_menu() to create a Bootstrap-compatible nav menu.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $args Array of wp_nav_menu() arguments.
	 * @return string The nav menu output.
	 */
	public static function render( $args = array() ) {
		if ( ! isset( $args['container'] ) ) {
			$args['container'] = false;
		}
		if ( ! isset( $args['items_wrap'] ) ) {
			$args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
		}

		if ( isset( $args['menu_class'] ) ) {
			$menu_classes = explode( ' ', $args['menu_class'] );

			if ( in_array( 'list-inline', $menu_classes, true ) ) {
				$args['li_class'] = 'list-inline-item';
				$args['depth'] = 1;
			} elseif ( in_array( 'navbar-nav', $menu_classes, true ) ) {
				$args['li_class'] = 'nav-item';
				$args['a_class'] = 'nav-link';
			}

			if ( in_array( 'social-menu', $menu_classes, true ) ) {
				$args['link_before'] = '<span class="sr-only">';
				$args['link_after'] = '</span>';
			}
		}

		$args['walker'] = new self();

		if ( isset( $args['li_class'] ) ) {
			self::$li_class = $args['li_class'];
			unset( $args['li_class'] );
		}
		if ( isset( $args['a_class'] ) ) {
			self::$a_class = $args['a_class'];
			unset( $args['a_class'] );
		}

		$output = \wp_nav_menu( $args );

		self::$li_class = '';
		self::$a_class = '';

		return $output;
	}

	/**
	 * Adds general filters to make nav menus compatible with Bootstrap and clean up some classes.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		add_filter( 'nav_menu_css_class', array( __CLASS__, 'fix_css_classes' ), 10, 4 );
		add_filter( 'nav_menu_link_attributes', array( __CLASS__, 'fix_link_attributes' ), 10, 4 );
		add_filter( 'nav_menu_item_id', '__return_null' );
	}

	/**
	 * Makes CSS classes of a nav menu item list element compatible with Bootstrap and replaces unnecessary bloat.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array    $classes Array of CSS classes.
	 * @param WP_Post  $item    Menu item data object.
	 * @param stdClass $args    An object of wp_nav_menu() arguments.
	 * @param int      $depth   Depth of menu item.
	 * @return array Modified CSS classes.
	 */
	public static function fix_css_classes( $classes, $item, $args, $depth ) {
		$slug = sanitize_title( $item->title );

		$classes = preg_replace( '/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', 'active', $classes );
		$classes = preg_replace( '/^((menu|page)[-_\w+]+)+/', '', $classes );
		$classes[] = 'menu-' . $slug;

		if ( ! empty( self::$li_class ) ) {
			$classes[] = self::$li_class;
		}

		$classes = array_unique( $classes );

		return array_filter( $classes, array( __CLASS__, 'is_class_valid' ) );
	}

	/**
	 * Makes attributes of a nav menu item link compatible with Bootstrap.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array    $atts  Array of link attributes.
	 * @param WP_Post  $item  Menu item data object.
	 * @param stdClass $args  An object of wp_nav_menu() arguments.
	 * @param int      $depth Depth of menu item.
	 * @return array Modified link attributes.
	 */
	public static function fix_link_attributes( $atts, $item, $args, $depth ) {
		if ( ! empty( self::$a_atts_override ) ) {
			$atts = array_merge( $atts, self::$a_atts_override );
			self::$a_atts_override = array();
		} elseif ( ! empty( self::$a_class ) ) {
			$atts = array_merge( array( 'class' => self::$a_class ), $atts );
		}

		return $atts;
	}

	/**
	 * Checks whether a string is a valid class.
	 *
	 * It is basically a check whether the trimmed version of the string is not empty.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $class The class to check.
	 * @return bool True if the class is valid, false otherwise.
	 */
	public static function is_class_valid( $class ) {
		$class = trim( $class );

		return empty( $class ) ? false : true;
	}
}
