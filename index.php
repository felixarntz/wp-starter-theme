<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

get_header(); ?>

			<div class="container">
				<div class="row">

					<main id="main" class="col-md-<?php echo ( is_active_sidebar( 'primary' ) ? 9 : 12 ); ?>" role="main">

						<?php if( have_posts() ) : ?>
							<?php while( have_posts() ) : the_post(); ?>
								<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
									<?php edit_post_link( null, '<p class="text-right">', '</p>' ); ?>
									<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail(); ?>
									<?php endif; ?>
									<?php the_content(); ?>
								</article>
							<?php endwhile; ?>
						<?php endif; ?>

					</main>

					<?php get_sidebar( 'primary' ); ?>

				</div>
			</div>

<?php get_footer();
