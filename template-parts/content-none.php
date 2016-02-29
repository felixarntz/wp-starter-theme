<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?>
<header>
	<h1 class="main-title"><?php _e( 'Nothing Found', 'wp-starter-theme' ); ?></h1>
</header>

<div class="content">
	<?php if ( is_search() ) : ?>
		<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wp-starter-theme' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php _e( 'It seems we cannot find what you&rsquo;re looking for. Perhaps searching can help.', 'wp-starter-theme' ); ?>
		<?php get_search_form(); ?>
	<?php endif; ?>
</p>
