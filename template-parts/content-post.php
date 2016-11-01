<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

?>
<article id="post-<?php echo $post->get_ID(); ?>" <?php echo $post->get_classes( '', true ); ?>>
	<?php if ( $singular ) : ?>
		<h1 class="post-title"><?php echo $post->get_data( 'title', true ); ?></h1>
	<?php else : ?>
		<h2 class="post-title"><a href="<?php echo $post->get_url(); ?>"><?php echo $post->get_data( 'title', true ); ?></a></h2>
	<?php endif; ?>
	<?php if ( $post->has_thumbnail() ) : ?>
		<?php if ( $singular ) : ?>
			<?php echo $post->get_thumbnail(); ?>
		<?php else : ?>
			<a href="<?php echo $post->get_url(); ?>"><?php echo $post->get_thumbnail(); ?></a>
		<?php endif; ?>
	<?php endif; ?>
	<?php the_post_meta(); ?>
	<div class="post-content">
		<?php echo $post->get_data( 'content', true ); ?>
		<?php wp_link_pages(); ?>
	</div>
	<?php comments_template(); ?>
</article>
