<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme;

$label = 'pwbox-' . ( ! $post || empty( $post->ID ) ? rand() : $post->ID );
?>
<form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" method="post" class="post-password-form form-inline">
	<p><?php __( 'This content is password protected. To view it please enter your password below:', 'wp-starter-theme' ); ?></p>
	<fieldset>
		<div class="input-group">
			<label class="sr-only" for="<?php echo $label; ?>"><?php _e( 'Password', 'wp-starter-theme' ); ?></label>
			<input type="password" id="<?php echo $label; ?>" name="post_password" class="form-control" maxlength="20">
			<span class="input-group-btn">
				<button type="submit" class="btn btn-secondary"><?php _e( 'Submit', 'wp-starter-theme' ); ?></button>
			</span>
		</div>
	</fieldset>
</form>
