<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

final class NavMenu extends \Walker_Nav_Menu {
	private static $li_class = '';
	private static $a_class = '';

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$html = '';

		parent::start_lvl( $html, $depth, $args );

		$html = str_replace( 'sub-menu', 'dropdown-menu', $html );

		$output .= $html;
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$html = '';

		parent::start_el( $html, $item, $depth, $args );

		if ( $item->is_dropdown && ( $depth === 0 ) ) {
			if ( false !== strpos( $html, '<a class="' ) ) {
				$html = str_replace( '<a class="', '<a data-toggle="dropdown" data-target="#" class="dropdown-toggle ', $html );
			} else {
				$html = str_replace( '<a', '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"', $html );
			}
			$html = str_replace( '</a>', ' <b class="caret"></b></a>', $html );
		} elseif( stristr( $html, 'li class="divider' ) ) {
			$html = preg_replace( '/<a[^>]*>.*?<\/a>/iU', '', $html );
		} elseif( stristr( $html, 'li class="dropdown-header' ) ) {
			$html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '$1', $html );
		}

		$output .= $html;
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		$element->is_dropdown = ( ( !empty( $children_elements[ $element->ID ] ) && ( ( $depth + 1 ) < $max_depth || ( $max_depth === 0 ) ) ) );

		if ( $element->is_dropdown ) {
			$element->classes[] = 'dropdown';
		}

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

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

	public static function init() {
		add_filter( 'nav_menu_css_class', array( __CLASS__, 'fix_css_classes' ), 10, 4 );
		add_filter( 'nav_menu_link_attributes', array( __CLASS__, 'fix_link_attributes' ), 10, 4 );
		add_filter( 'nav_menu_item_id', '__return_null' );
	}

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

	public static function fix_link_attributes( $atts, $item, $args, $depth ) {
		if ( ! empty( self::$a_class ) ) {
			$atts = array_merge( array( 'class' => self::$a_class ), $atts );
		}

		return $atts;
	}

	public static function is_class_valid( $class ) {
		$class = trim( $class );

		return empty( $class ) ? false : true;
	}
}
