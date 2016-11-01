<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<a class="skip-link screen-reader-text" href="#main"><?php _e( 'Skip to content', 'wp-starter-theme' ); ?></a>

	<header id="header" class="site-header" role="banner">

		<div class="container">

			<?php get_template_part( 'template-parts/header', 'branding' ); ?>
			<?php get_template_part( 'template-parts/header', 'navigation' ); ?>

		</div>

	</header><!-- #header -->
