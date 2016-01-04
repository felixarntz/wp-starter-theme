<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

final class Template {
	public static function render( $slug, $args = array(), $echo = true, $cache = true ) {
		//TODO
	}

	public static function edit_link( $text = null, $before = '', $after = '', $id = 0, $class = 'post-edit-link' ) {
		if ( null === $text ) {
			$post_type = self::get_post_type_object();
			$text = sprintf( __( 'Edit %s', 'wp-starter-theme' ), $post_type->labels->singular_name );
		}

		\edit_post_link( $text, $before, $after, $id, $class );
	}

	public static function get_post_type_object( $post_type = null ) {
		if ( null === $post_type ) {
			if ( $post = get_post() ) {
				$post_type = $post->post_type;
			} else {
				$post_type = 'post';
			}
		}

		return \get_post_type_object( $post_type );
	}
}
