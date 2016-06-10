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

					<?php if( have_posts() ) : ?>

						<header>
							<?php
							the_archive_title( '<h1 class="main-title">', '</h1>' );
							the_archive_description( '<div class="main-description">', '</div>' );
							?>
						</header>

						<?php

						while( have_posts() ) : the_post();

							$slug = 'content';
							$name = get_post_type();
							if ( 'post' === $name ) {
								$slug .= '-post';
								$name = get_post_format();
							}
							get_template_part( 'template-parts/' . $slug, array(
								'name'		=> $name,
								'post'		=> \WPOO\Post::get( get_the_ID() ),
								'singular'	=> false,
							), true );

						endwhile;

						?>
					<?php else : ?>
						<?php get_template_part( 'template-parts/content', 'none' ); ?>
					<?php endif; ?>

				</main>

				<?php get_sidebar( 'primary' ); ?>

			</div>
		</div>

<?php get_footer();
