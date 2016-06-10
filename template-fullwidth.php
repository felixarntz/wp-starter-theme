<?php
/**
 * Template Name: Fullwidth
 *
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

get_header(); ?>

		<main id="main" class="site-content" role="main">

			<?php
			while( have_posts() ) : the_post();

				get_template_part( 'template-parts/content', array(
					'name'		=> get_post_type(),
					'post'		=> \WPOO\Post::get( get_the_ID() ),
					'singular'	=> true,
				), true );

			endwhile;
			?>

		</main>

<?php get_footer();
