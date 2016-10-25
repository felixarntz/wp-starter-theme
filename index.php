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

						<?php if ( is_home() && ! is_front_page() ) : ?>
							<header>
								<h1 class="main-title"><?php single_post_title(); ?></h1>
							</header>
						<?php endif; ?>

						<div id="posts-list" class="posts-list">
							<?php theme()->partials()->render_loop(); ?>
						</div>

					<?php else : ?>
						<?php get_template_part( 'template-parts/content', 'none' ); ?>
					<?php endif; ?>

				</main>

				<?php get_sidebar( 'primary' ); ?>

			</div>
		</div>

<?php get_footer();
