<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?>
<?php if ( has_nav_menu( 'primary' ) ) : ?>
	<nav class="main-navigation navbar navbar-light bg-faded" role="navigation" aria-label="<?php _e( 'Main Navigation', 'wp-starter-theme' ); ?>">
		<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav navbar-nav' ) ); ?>
	</nav>
<?php endif; ?>
