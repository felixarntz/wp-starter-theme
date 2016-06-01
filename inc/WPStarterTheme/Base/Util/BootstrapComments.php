<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

final class BootstrapComments {
	public static function wp_list_comments( $args = array() ) {
		$echo = ! isset( $args['echo'] ) || $args['echo'];

		$args['style'] = 'div';
		$args['callback'] = array( __CLASS__, '_render_comment' );
		$args['end-callback'] = array( __CLASS__, '_end_comment' );
		$args['echo'] = false;

		if ( ! isset( $args['avatar_size'] ) ) {
			$args['avatar_size'] = 120;
		}

		$output = \wp_list_comments( $args );

		if ( ! $output ) {
			return '';
		}

		$output = '<ul class="comment-list media-list">' . $output . '</ul>';

		if ( isset( $args['before'] ) ) {
			$output = $args['before'] . $output;
		}
		if ( isset( $args['after'] ) ) {
			$output .= $args['after'];
		}

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}
	}

	public static function comment_form( $args = array() ) {
		$commenter = wp_get_current_commenter();

		$req = get_option( 'require_name_email' );
		$required_attr = 'aria-required="true" required';
		$required_indicator = ' <span class="required">*</span>';

		$args['fields'] = array(
			'author'	=> '<div class="comment-form-author form-group row"><label class="control-label col-sm-3" for="author">' . __( 'Name', 'wp-starter-theme' ) . ( $req ? $required_indicator : '' ) . '</label><div class="col-sm-9"><input type="text" id="author" name="author" class="form-control" value="' . esc_attr( $commenter['comment_author'] ) . '"' . ( $req ? $required_attr : '' ) . '></div></div>',
			'email'		=> '<div class="comment-form-email form-group row"><label class="control-label col-sm-3" for="email">' . __( 'Email', 'wp-starter-theme' ) . ( $req ? $required_indicator : '' ) . '</label><div class="col-sm-9"><input type="email" id="email" name="email" class="form-control" value="' . esc_attr( $commenter['comment_author_email'] ) . '"' . ( $req ? $required_attr : '' ) . '></div></div>',
			'url'		=> '<div class="comment-form-url form-group row"><label class="control-label col-sm-3" for="url">' . __( 'Website', 'wp-starter-theme' ) . '</label><div class="col-sm-9"><input type="url" id="url" name="url" class="form-control" value="' . esc_attr( $commenter['comment_author_url'] ) . '"></div></div>',
		);
		$args['comment_field'] = '<div class="comment-form-comment form-group row"><label class="control-label col-sm-3" for="comment">' . __( 'Comment', 'wp-starter-theme' ) . $required_indicator . '</label><div class="col-sm-9"><textarea id="comment" name="comment" class="form-control" rows="8"' . $required_attr . '></textarea></div></div>';
		$args['submit_field'] = '<div class="form-submit form-group row"><div class="col-sm-9 col-sm-offset-3">%1$s %2$s</div></div>';
		$args['class_submit'] = 'submit btn btn-primary';
		$args['format'] = 'html5';

		\comment_form( $args );
	}

	public static function _render_comment( $comment, $args, $depth ) {
		$tag = 'li';
		$add_below = 'comment';
		if ( 1 < $depth ) {
			$tag = 'div';
		}

		?>
		<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'media', $comment ); ?>>
			<?php if ( ( 'pingback' === $comment->comment_type || 'trackback' === $comment->comment_type ) && $args['short_ping'] ) : ?>
				<div class="media-body">
					<h4><?php _e( 'Pingback:', 'wp-starter-theme' ); ?> <?php comment_author_link( $comment ); ?></h4>
					<div class="comment-metadata">
						<?php edit_comment_link( __( 'Edit Pingback', 'wp-starter-theme' ), '<span class="edit-pingback">', '</span>' ); ?>
					</div>
			<?php else : ?>
				<?php if ( 0 != $args['avatar_size'] ) : ?>
					<div class="media-left">
						<?php if ( get_comment_author_url( $comment ) ) : ?>
							<a href="<?php comment_author_url( $comment ); ?>" rel="external nofollow">
								<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
							</a>
						<?php else : ?>
							<span>
								<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="media-body">
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<div class="alert alert-info">
							<p><?php _e( 'Your comment is awaiting moderation.', 'wp-starter-theme' ); ?></p>
						</div>
					<?php endif; ?>
					<?php TemplateTags::the_comment_meta( $comment, $add_below, $depth, $args['max_depth'] ); ?>
					<div class="comment-content">
						<?php comment_text( get_comment_id(), array_merge( $args, array(
							'add_below'	=> $add_below,
							'depth'		=> $depth,
							'max_depth'	=> $args['max_depth'],
						) ) ); ?>
					</div>
			<?php endif; ?>
		<?php
	}

	public static function _end_comment( $comment, $args, $depth ) {
		$tag = 'li';
		if ( 0 < $depth ) {
			$tag = 'div';
		}
		?>
			</div>
		</<?php echo $tag; ?>>
		<?php
	}
}
