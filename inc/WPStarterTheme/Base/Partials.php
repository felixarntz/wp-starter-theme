<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

/**
 * Class to render partials that go beyond a template.
 *
 * @since 1.0.0
 */
final class Partials extends ThemeUtilityBase {
	/**
	 * Adds the necessary hooks.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function run() {
		// Empty method body.
	}

	/**
	 * Renders the site title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_blogname() {
		bloginfo( 'name' );
	}

	/**
	 * Renders the site description.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_blogdescription() {
		bloginfo( 'description' );
	}

	/**
	 * Renders a regular posts loop.
	 *
	 * @since 1.0.0
	 * @access public
	 */
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
