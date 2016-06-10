<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

get_header(); ?>

		<div class="container">
			<div class="row">

				<main id="main" class="site-content col-md-<?php echo ( is_active_sidebar( 'primary' ) ? 9 : 12 ); ?>" role="main">

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

				<?php get_sidebar( 'primary' ); ?>

			</div>
		</div>

<?php get_footer();
