<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?>
<article id="post-<?php echo $post->get_ID(); ?>" <?php echo $post->get_classes( '', true ); ?>>
	<h1>
		<?php echo $post->get_data( 'title', true ); ?>
	</h1>
	<?php if ( $post->has_thumbnail() ) : ?>
		<?php echo $post->get_thumbnail(); ?>
	<?php endif; ?>
	<?php the_post_meta(); ?>
	<?php echo $post->get_data( 'content', true ); ?>
	<?php comments_template(); ?>
</article>
