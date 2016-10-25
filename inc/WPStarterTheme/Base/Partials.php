<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

final class Partials {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
	}

	public function render_blogname() {
		bloginfo( 'name' );
	}

	public function render_blogdescription() {
		bloginfo( 'description' );
	}

	public function render_loop() {
		while ( have_posts() ) {
			the_post();
			$slug = 'content';
			$name = get_post_type();
			if ( 'post' === $name ) {
				$slug .= '-post';
				$name = get_post_format();
			}

			\WPStarterTheme\get_template_part( 'template-parts/' . $slug, array(
				'name'		=> $name,
				'post'		=> \WPOO\Post::get( get_the_ID() ),
				'singular'	=> false,
			), true );
		}
	}
}
