<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?>
<header class="main-header">
	<h1 class="main-title"><?php _e( 'Nothing Found', 'wp-starter-theme' ); ?></h1>
</header>

<div class="post-content">
	<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
		<p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'wp-starter-theme' ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>
	<?php elseif ( is_search() ) : ?>
		<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wp-starter-theme' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php _e( 'It seems we cannot find what you&rsquo;re looking for. Perhaps searching can help.', 'wp-starter-theme' ); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
</div>
