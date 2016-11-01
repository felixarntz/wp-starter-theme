<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?>
<?php if ( is_front_page() ) : ?>
	<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php theme()->partials()->render_blogname(); ?></a></h1>
<?php else : ?>
	<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php theme()->partials()->render_blogname(); ?></a></p>
<?php endif; ?>

<?php if ( get_bloginfo( 'description' ) || is_customize_preview() ) : ?>
	<p class="site-description"><?php theme()->partials()->render_blogdescription(); ?></p>
<?php endif; ?>

<?php the_custom_logo(); ?>
