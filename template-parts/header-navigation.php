<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?>
<?php if ( has_nav_menu( 'primary' ) ) : ?>
	<nav class="navbar navbar-light bg-faded" role="navigation">
		<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav navbar-nav' ) ); ?>
	</nav>
<?php endif; ?>
