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

		<header id="header" role="banner">

			<div class="container">

				<h1><a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
				<h2><?php bloginfo( 'description' ); ?></h2>

				<?php if ( has_nav_menu( 'primary' ) ) : ?>
					<nav role="navigation">
						<?php wp_nav_menu( 'theme_location' => 'primary' ); ?>
					</nav>
				<?php endif; ?>

			</div>

		</header><!-- #header -->

		<div id="content">
