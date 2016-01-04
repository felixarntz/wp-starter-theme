<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

function theme() {
	return Base\Theme::instance();
}

function add_image_size( $name, $width, $height, $crop = false, $selectable = false ) {
	Base\Util\Images::add_size( $name, $width, $height, $crop, $selectable );
}

function wp_nav_menu( $args = array() ) {
	if ( ! isset( $args['container'] ) ) {
		$args['container'] = false;
	}
	if ( ! isset( $args['items_wrap'] ) ) {
		$args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
	}

	if ( isset( $args['theme_location'] ) && 'primary' === $args['theme_location'] ) {
		$args['depth'] = 2;
		$args['walker'] = new Base\Util\NavWalker();
	}

	return \wp_nav_menu( $args );
}

function get_template_part( $slug, $args = array(), $echo = true, $cache = true ) {
	Base\Util\Template::render( $slug, $args, $echo, $cache );
}

function edit_post_link( $text = null, $before = '', $after = '', $id = 0, $class = 'post-edit-link' ) {
	Base\Util\Template::edit_link( $text, $before, $after, $id, $class );
}

function get_post_type_object( $post_type = null ) {
	return Base\Util\Template::get_post_type_object( $post_type );
}
