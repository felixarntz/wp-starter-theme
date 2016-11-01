<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>

		<a class="skip-link screen-reader-text" href="#main"><?php _e( 'Skip to content', 'wp-starter-theme' ); ?></a>

		<header id="header" class="site-header" role="banner">

			<div class="container">

				<h1 class="site-title"><a href="<?php bloginfo( 'url' ); ?>"><?php theme()->partials()->render_blogname(); ?></a></h1>
				<h2 class="site-description"><?php theme()->partials()->render_blogdescription(); ?></h2>

				<?php if ( function_exists( 'the_custom_logo' ) ) : ?>
					<?php the_custom_logo(); ?>
				<?php endif; ?>

				<?php if ( has_nav_menu( 'primary' ) ) : ?>
					<nav class="navbar navbar-light bg-faded" role="navigation">
						<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav navbar-nav' ) ); ?>
					</nav>
				<?php endif; ?>

			</div>

		</header><!-- #header -->
