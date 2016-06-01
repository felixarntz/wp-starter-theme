<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

if ( post_password_required() ) {
	return;
}

if ( ! have_comments() && ! comments_open() ) {
	return;
}

?>
<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title"><?php printf( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'wp-starter-theme' ), number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' ); ?></h2>
		<?php the_comments_navigation(); ?>
		<?php
		wp_list_comments( array(
			'type' 		=> 'comment',
		) );
		wp_list_comments( array(
			'type'		=> 'pings',
			'before'	=> '<h3>' . __( 'Pingbacks / Trackbacks', 'wp-starter-theme' ),
		) );
		?>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if ( comments_open() ) : ?>
		<?php comment_form(); ?>
	<?php elseif ( 0 != get_comments_number() ) : ?>
		<div class="no-comments alert alert-info">
			<p><?php _e( 'Comments are closed.', 'wp-starter-theme' ); ?></p>
		</div>
	<?php endif; ?>

</div>
