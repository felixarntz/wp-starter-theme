<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

/**
 * Class to handle several template tags in a Bootstrap-compatible way.
 *
 * @since 1.0.0
 */
final class TemplateTags {
	/**
	 * Whether to display relative dates.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var bool
	 */
	private static $relative_dates = false;

	/**
	 * Post types for which to show the post date.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var array
	 */
	private static $show_post_date = array( 'post' );

	/**
	 * Displays post meta for a given post.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Post|int|null $post Optional. Post object or ID. Default is the current post.
	 */
	public static function the_post_meta( $post = null ) {
		echo self::get_the_post_meta( $post );
	}

	/**
	 * Returns post meta output for a given post.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Post|int|null $post Optional. Post object or ID. Default is the current post.
	 * @return The post meta output.
	 */
	public static function get_the_post_meta( $post = null ) {
		$post = get_post( $post );

		if ( ! $post ) {
			return;
		}

		$output = '';

		if ( in_array( $post->post_type, self::$show_post_date, true ) ) {
			$output .= '<li class="post-date"><span class="screen-reader-text">' . _x( 'Posted on', 'Used before the post date.', 'wp-starter-theme' ) . ' </span><time datetime="' . esc_attr( get_post_time( 'c', false, $post ) ) . '">' . self::get_the_post_date( $post ) . '</time></li>';

			if ( self::is_modified_different( $post ) ) {
				$output .= '<li class="post-modified-date"><span class="screen-reader-text">' . _x( 'Last Edited on', 'Used before the post modified date.', 'wp-starter-theme' ) . ' </span><time datetime="' . esc_attr( get_post_modified_time( 'c', false, $post ) ) . '">' . self::get_the_post_modified_date( $post ) . '</time></li>';
			}
		}

		if ( post_type_supports( $post->post_type, 'author' ) && is_multi_author() ) {
			$output .= '<li class="post-author"><span class="screen-reader-text">' . _x( 'Author', 'Used before the post author name.', 'wp-starter-theme' ) . ' </span>' . get_the_author_posts_link() . '</li>';
		}

		switch ( $post->post_type ) {
			case 'attachment':
				//TODO
				break;
			case 'page':
				break;
			case 'post':
				if ( self::is_multi_categories() ) {
					$output .= get_the_term_list( $post->ID, 'category', '<li class="post-categories"><span class="screen-reader-text">' . _x( 'Categories', 'Used before category names.', 'leavesandlove-v5' ) . ' </span>', ', ', '</li>' );
				}
				if ( self::is_multi_tags() ) {
					$output .= get_the_term_list( $post->ID, 'post_tag', '<li class="post-tags"><span class="screen-reader-text">' . _x( 'Tags', 'Used before tag names.', 'leavesandlove-v5' ) . ' </span>', ', ', '</li>' );
				}
				if ( current_theme_supports( 'post-formats' ) && self::is_multi_post_formats() ) {
					$output .= '<li class="post-format"><span class="screen-reader-text">' . _x( 'Post Format', 'Used before the post format.', 'leavesandlove-v5' ) . ' </span>' . self::get_the_post_format( $post ) . '</li>';
				}
				break;
			default:
				$taxonomies = get_object_taxonomies( $post );
				foreach ( $taxonomies as $taxonomy ) {
					if ( 'post_format' === $taxonomy ) {
						continue;
					}

					$class = 'post-';
					if ( 'category' === $taxonomy ) {
						$class .= 'categories';
					} elseif ( 'post_tag' === $taxonomy ) {
						$class .= 'tags';
					} else {
						$class .= $taxonomy;
					}
					if ( self::is_multi_terms( $taxonomy ) ) {
						$taxonomy_label = _x( 'Terms', 'Used before term names.', 'wp-starter-theme' );
						if ( false !== ( $taxonomy_obj = get_taxonomy( $taxonomy ) ) ) {
							$taxonomy_label = $taxonomy_obj->labels->name;
						}
						$output .= get_the_term_list( $post->ID, $taxonomy, '<li class="' . $class . '"><span class="screen-reader-text">' . $taxonomy_label . ' </span>', ', ', '</li>' );
					}
				}
				if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) && self::is_multi_post_formats() ) {
					$output .= '<li class="post-format"><span class="screen-reader-text">' . _x( 'Post Format', 'Used before the post format.', 'wp-starter-theme' ) . ' </span>' . self::get_the_post_format( $post ) . '</li>';
				}
		}

		if ( post_type_supports( $post->post_type, 'comments' ) && ! post_password_required( $post ) && ( comments_open( $post->ID ) || get_comments_number( $post->ID ) ) ) {
			$output .= '<li class="post-comments-link">' . self::get_the_comments_popup_link( $post ) . '</li>';
		}

		if ( is_user_logged_in() ) {
			ob_start();
			self::edit_post_link( null, '<li class="post-edit-link">', '</li>', $post->ID );
			$output .= ob_get_clean();
		}

		if ( ! empty( $output ) ) {
			$output = '<ul class="post-meta post-meta-' . $post->post_type . '">' . $output . '</ul>';
		}

		return $output;
	}

	/**
	 * Displays comment meta for a given comment.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Comment|int|null $comment   Optional. Comment object or ID. Default is the current comment.
	 * @param string              $add_below Where to add the comment text.
	 * @param int                 $depth     Depth of the comment.
	 * @param int                 $max_depth Maximum comment depth.
	 */
	public static function the_comment_meta( $comment = null, $add_below = 'comment', $depth = 0, $max_depth = 0 ) {
		echo self::get_the_comment_meta( $comment, $add_below, $depth, $max_depth );
	}

	/**
	 * Returns comment meta output for a given comment.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Comment|int|null $comment   Optional. Comment object or ID. Default is the current comment.
	 * @param string              $add_below Where to add the comment text.
	 * @param int                 $depth     Depth of the comment.
	 * @param int                 $max_depth Maximum comment depth.
	 * @return The comment meta output.
	 */
	public static function get_the_comment_meta( $comment = null, $add_below = 'comment', $depth = 0, $max_depth = 0 ) {
		$comment = get_comment( $comment );

		if ( ! $comment ) {
			return;
		}

		$output = '<li class="comment-date"><span class="screen-reader-text">' . _x( 'Posted on', 'Used before the comment date.', 'wp-starter-theme' ) . '</span><time datetime="' . esc_attr( self::get_comment_time( 'c', false, $comment ) ) . '">' . self::get_the_comment_date( $comment ) . '</time></li>';

		$output .= '<li class="comment-author"><span class="screen-reader-text">' . _x( 'Author', 'Used before the comment author name.', 'wp-starter-theme' ) . '</span>' . get_comment_author_link( $comment ) . '</li>';

		if ( 'comment' === $comment->comment_type ) {
			$output .= get_comment_reply_link( array(
				'add_below'	=> $add_below,
				'depth'		=> $depth,
				'max_depth'	=> $max_depth,
				'before'	=> '<li class="comment-reply-link-wrap">',
				'after'		=> '</li>',
			), $comment );
		}

		ob_start();
		self::edit_comment_link( null, '<li class="comment-edit-link">', '</li>', $comment );
		$output .= ob_get_clean();

		$comment_type = $comment->comment_type ? $comment->comment_type : 'comment';
		$output = '<ul class="comment-meta comment-meta-' . $comment_type . '">' . $output . '</ul>';

		return $output;
	}

	/**
	 * Whether the post date is different from the post modified date.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Post $post Post object.
	 * @return bool True if the dates are different, false otherwise.
	 */
	public static function is_modified_different( $post = null ) {
		return get_post_time( 'Ymd', false, $post ) !== get_post_modified_time( 'Ymd', false, $post );
	}

	/**
	 * Displays the post date for a given post.
	 *
	 * Depending on the $relative_dates property, the date is displayed relative to the current date.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function the_post_date( $post = null ) {
		$output = self::get_the_post_date( $post );
		if ( $output ) {
			echo $output;
		}
	}

	/**
	 * Returns the post date output for a given post.
	 *
	 * Depending on the $relative_dates property, the date is displayed relative to the current date.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Post $post Post object.
	 * @return string The post date output.
	 */
	public static function get_the_post_date( $post = null ) {
		if ( self::$relative_dates ) {
			return self::human_time_diff( mysql2date( 'U', $post->post_date ), current_time( 'timestamp' ), true );
		}

		return get_the_date( '', $post );
	}

	/**
	 * Displays the post modified date for a given post.
	 *
	 * Depending on the $relative_dates property, the date is displayed relative to the current date.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function the_post_modified_date( $post = null ) {
		$output = self::get_the_post_modified_date( $post );
		if ( $output ) {
			echo $output;
		}
	}

	/**
	 * Returns the post modified date output for a given post.
	 *
	 * Depending on the $relative_dates property, the date is displayed relative to the current date.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Post $post Post object.
	 * @return string The post modified date output.
	 */
	public static function get_the_post_modified_date( $post = null ) {
		if ( self::$relative_dates ) {
			return self::human_time_diff( mysql2date( 'U', $post->post_modified ), current_time( 'timestamp' ), true );
		} else {
			return get_post_modified_time( get_option( 'date_format' ), false, $post, true );
		}
	}

	/**
	 * Displays the comment date for a given comment.
	 *
	 * Depending on the $relative_dates property, the date is displayed relative to the current date.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Comment $comment Comment object.
	 */
	public static function the_comment_date( $comment = null ) {
		$output = self::get_the_comment_date( $comment );
		if ( $output ) {
			echo $output;
		}
	}

	/**
	 * Returns the comment date output for a given comment.
	 *
	 * Depending on the $relative_dates property, the date is displayed relative to the current date.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Comment $comment Comment object.
	 * @return string The comment date output.
	 */
	public static function get_the_comment_date( $comment = null ) {
		if ( self::$relative_dates ) {
			return self::human_time_diff( mysql2date( 'U', $comment->comment_date ), current_time( 'timestamp' ), true );
		}

		return self::get_comment_time( '', false, $comment );
	}

	/**
	 * Returns a human-readable time difference between two timestamps.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param int  $compare The target timestamp.
	 * @param int  $current Optional. The current timestamp. Default to now.
	 * @param bool $format  Optional. Whether to format the output. Default false.
	 * @return string The time difference.
	 */
	public static function human_time_diff( $compare, $current = '', $format = false ) {
		if ( empty( $current ) ) {
			$current = time();
		}

		$output = \human_time_diff( $compare, $current );

		if ( $format ) {
			if ( $current < $compare ) {
				$output = sprintf( __( 'in %s', 'wp-starter-theme' ), $output );
			} else {
				$output = sprintf( __( '%s ago', 'wp-starter-theme' ), $output );
			}
		}

		return $output;
	}

	/**
	 * Displays the post format link for a given post.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Post|int|null $post Optional. Post object or ID. Default is the current post.
	 */
	public static function the_post_format( $post = null ) {
		echo self::get_the_post_format( $post );
	}

	/**
	 * Returns the post format link output for a given post.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Post|int|null $post Optional. Post object or ID. Default is the current post.
	 * @return string The post format link output.
	 */
	public static function get_the_post_format( $post = null ) {
		$format = get_post_format( $post );
		if ( ! $format ) {
			$format = 'standard';
		}

		$output = get_post_format_string( $format );
		$link = get_post_format_link( $format );

		if ( $link ) {
			$output = '<a href="' . $link . '">' . $output . '</a>';
		}

		return $output;
	}

	/**
	 * Displays the comments popup link for a given post.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Post|int|null $post Optional. Post object or ID. Default is the current post.
	 */
	public static function the_comments_popup_link( $post = null ) {
		echo self::get_the_comments_popup_link( $post );
	}

	/**
	 * Returns the comments popup link output for a given post.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param WP_Post|int|null $post Optional. Post object or ID. Default is the current post.
	 * @return string The comments popup link output.
	 */
	public static function get_the_comments_popup_link( $post = null ) {
		$post = get_post( $post );

		$count = 0;
		if ( $post ) {
			$count = get_comments_number( $post->ID );
		}

		$output = __( 'No Comments', 'wp-starter-theme' );
		if ( 0 === $count ) {
			$output = sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'wp-starter-theme' ), get_the_title( $post->ID ) );
		} else {
			$output = sprintf( _n( '%1$s Comment<span class="screen-reader-text"> on %2$s</span>', '%1$s Comments<span class="screen-reader-text"> on %2$s</span>', $count, 'wp-starter-theme' ), number_format_i18n( $count ), get_the_title( $post->ID ) );
		}

		$link = get_comments_link( $post->ID );

		return '<a href="' . $link . '">' . $output . '</a>';
	}

	/**
	 * Returns the comment time for a given comment.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string         $d         Optional. Date format string. Default is the time_format setting.
	 * @param bool           $gmt       Optional. Whether to use the GMT time. Default false.
	 * @param WP_Comment|int $comment   Optional. Comment object or ID. Default is the current comment.
	 * @param bool           $translate Optional. Whether to translate the date. Default true.
	 * @return string The comment time output.
	 */
	public static function get_comment_time( $d = '', $gmt = false, $comment = 0, $translate = true ) {
		// compatibility with original function signature
		if ( is_bool( $comment ) ) {
			$translate = $comment;
			$comment = 0;
		}

		$comment = get_comment( $comment );

		$comment_date = $gmt ? $comment->comment_date_gmt : $comment->comment_date;
		if ( '' == $d )
			$date = mysql2date(get_option('time_format'), $comment_date, $translate);
		else
			$date = mysql2date($d, $comment_date, $translate);

		return apply_filters( 'get_comment_time', $date, $d, $gmt, $translate, $comment );
	}

	/**
	 * Displays the post edit link for a given post.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string|null $text   Optional. Anchor text for the link. Default empty.
	 * @param string      $before Optional. Content to display before the link. Default empty.
	 * @param string      $after  Optional. Content to display after the link. Default empty.
	 * @param WP_Post|int $post   Optional. Post object or ID. Default is the current post.
	 * @param string      $class  Optional. CSS class for the link tag. Default is 'post-edit-link'.
	 */
	public static function edit_post_link( $text = null, $before = '', $after = '', $post = 0, $class = 'post-edit-link' ) {
		if ( null === $text ) {
			$post = get_post( $post );
			$post_type = get_post_type_object( $post ? $post->post_type : 'post' );
			$text = sprintf( __( 'Edit %s', 'wp-starter-theme' ), $post_type->labels->singular_name );
		}

		\edit_post_link( $text, $before, $after, $post, $class );
	}

	/**
	 * Displays the comment edit link for a given comment.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string|null    $text    Optional. Anchor text for the link. Default empty.
	 * @param string         $before  Optional. Content to display before the link. Default empty.
	 * @param string         $after   Optional. Content to display after the link. Default empty.
	 * @param WP_Comment|int $comment Optional. Comment object or ID. Default is the current comment.
	 * @param string         $class   Optional. CSS class for the link tag. Default is 'comment-edit-link'.
	 */
	public static function edit_comment_link( $text = null, $before = '', $after = '', $comment = 0, $class = 'comment-edit-link' ) {
		$comment = get_comment( $comment );

		if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
			return;
		}

		if ( null === $text ) {
			$text = __( 'Edit This' );
		}

		$link = '<a class="' . esc_attr( $class ) . '" href="' . esc_url( get_edit_comment_link( $comment ) ) . '">' . $text . '</a>';

		/**
		 * Filter the comment edit link anchor tag.
		 *
		 * @since 2.3.0
		 *
		 * @param string $link       Anchor tag for the edit link.
		 * @param int    $comment_id Comment ID.
		 * @param string $text       Anchor text.
		 */
		echo $before . apply_filters( 'edit_comment_link', $link, $comment->comment_ID, $text ) . $after;
	}

	/**
	 * Checks whether multiple categories exist on the site.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return bool True if more than one category exists, false otherwise.
	 */
	public static function is_multi_categories() {
		return self::is_multi_terms( 'category' );
	}

	/**
	 * Checks whether multiple tags exist on the site.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return bool True if more than one tag exists, false otherwise.
	 */
	public static function is_multi_tags() {
		return self::is_multi_terms( 'post_tag' );
	}

	/**
	 * Checks whether multiple post formats exist on the site.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return bool True if more than one post format exists, false otherwise.
	 */
	public static function is_multi_post_formats() {
		return self::is_multi_terms( 'post_format' );
	}

	/**
	 * Checks whether multiple terms of a given taxonomy exist on the site.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $taxonomy Name of the taxonomy.
	 * @return bool True if more than one term of the taxonomy exists, false otherwise.
	 */
	public static function is_multi_terms( $taxonomy ) {
		global $wp_version;

		$transient_name = 'wp_starter_theme_is_multi_' . $taxonomy;

		$is_multi_terms = 0;

		if ( false === ( $is_multi_terms = get_transient( $transient_name ) ) ) {
			if ( version_compare( $wp_version, '4.5.0', '<' ) ) {
				$is_multi_terms = get_terms( $taxonomy, array(
					'fields'		=> 'count',
					'hide_empty'	=> 1,
					'number'		=> 2,
				) );
			} else {
				$is_multi_terms = get_terms( array(
					'taxonomy'		=> $taxonomy,
					'fields'		=> 'count',
					'hide_empty'	=> 1,
					'number'		=> 2,
				) );
			}

			$is_multi_terms = intval( $is_multi_terms ) > 1 ? 1 : 0;

			set_transient( $transient_name, $is_multi_terms );
		}

		return (bool) $is_multi_terms;
	}

	/**
	 * Adds general filters to handle transient deletion for the multiple terms checks.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		add_action( 'save_post', array( __CLASS__, '_clear_is_multi_terms_post_cache' ), 10, 1 );
	}

	/**
	 * Clears the multiple terms transients for all taxonomies associated with the given post.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @internal
	 *
	 * @param int $post_id Post ID.
	 */
	public static function _clear_is_multi_terms_post_cache( $post_id ) {
		self::_clear_is_multi_terms_cache( get_object_taxonomies( get_post( $post_id ) ) );
	}

	/**
	 * Clears the multiple terms transients for the given taxonomies.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param string|array $taxonomies One or more taxonomy names.
	 */
	private static function _clear_is_multi_terms_cache( $taxonomies ) {
		$taxonomies = (array) $taxonomies;

		foreach ( $taxonomies as $taxonomy ) {
			delete_transient( 'wp_starter_theme_is_multi_' . $taxonomy );
		}
	}
}
