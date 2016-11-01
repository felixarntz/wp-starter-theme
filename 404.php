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

				<header>
					<h1 class="main-title"><?php _e( 'Error 404 - Not Found', 'wp-starter-theme' ); ?></h1>
				</header>

				<div class="content">
					<p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'wp-starter-theme' ); ?></p>
					<?php get_search_form(); ?>
				</div>

			</main>

			<?php get_sidebar( 'primary' ); ?>

		</div>
	</div>

<?php get_footer();
