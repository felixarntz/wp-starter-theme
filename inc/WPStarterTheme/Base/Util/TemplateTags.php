<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

final class TemplateTags {
	private static $relative_dates = false;
	private static $show_post_date = array( 'post' );

	public static function the_post_meta( $post = null ) {
		echo self::get_the_post_meta( $post );
	}

	public static function get_the_post_meta( $post = null ) {
		$post = get_post( $post );

		if ( ! $post ) {
			return;
		}

		$output = '<ul class="post-meta post-meta-' . $post->post_type . '">';

		if ( in_array( $post->post_type, self::$show_post_date, true ) ) {
			$output .= '<li class="post-date"><span class="screen-reader-text">' . _x( 'Posted on', 'Used before the post date.', 'wp-starter-theme' ) . ' </span><time datetime="' . esc_attr( get_post_time( 'c', false, $post ) ) . '">' . self::get_the_post_date( $post ) . '</time></li>';

			if ( get_post_time( 'U', false, $post ) !== get_post_modified_time( 'U', false, $post ) ) {
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
					$output .= get_the_category_list( '<li class="post-categories"><span class="screen-reader-text">' . _x( 'Categories', 'Used before category names.', 'wp-starter-theme' ) . ' </span>', ', ', '</li>', $post->ID );
				}
				if ( self::is_multi_tags() ) {
					$output .= get_the_tag_list( '<li class="post-tags"><span class="screen-reader-text">' . _x( 'Tags', 'Used before tag names.', 'wp-starter-theme' ) . ' </span>', ', ', '</li>', $post->ID );
				}
				if ( current_theme_supports( 'post-formats' ) && self::is_multi_post_formats() ) {
					$output .= '<li class="post-format"><span class="screen-reader-text">' . _x( 'Post Format', 'Used before the post format.', 'wp-starter-theme' ) . ' </span>' . self::get_the_post_format( $post ) . '</li>';
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
			edit_post_link( null, '<li class="post-edit-link">', '</li>' );
			$output = ob_get_clean();
		}

		return $output;
	}

	public static function the_post_date( $post = null ) {
		$output = self::get_the_post_date( $post );
		if ( $output ) {
			echo $output;
		}
	}

	public static function get_the_post_date( $post = null ) {
		if ( self::$relative_dates ) {
			return self::human_time_diff( mysql2date( 'U', $post->post_date ), current_time( 'timestamp' ), true );
		} else {
			return get_the_date( '', $post );
		}
	}

	public static function the_post_modified_date( $post = null ) {
		$output = self::get_the_post_modified_date( $post );
		if ( $output ) {
			echo $output;
		}
	}

	public static function get_the_post_modified_date( $post = null ) {
		if ( self::$relative_dates ) {
			return self::human_time_diff( mysql2date( 'U', $post->post_modified ), current_time( 'timestamp' ), true );
		} else {
			return get_post_modified_time( get_option( 'date_format' ), false, $post, true );
		}
	}

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

	public static function the_post_format( $post = null ) {
		echo self::get_the_post_format( $post );
	}

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

	public static function the_comments_popup_link( $post = null ) {
		echo self::get_the_comments_popup_link( $post );
	}

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
			$output = sprintf( _n( '%s Comment', '%s Comments', $count, 'wp-starter-theme' ), number_format_i18n( $count ) );
		}

		$link = get_comments_link( $post->ID );

		return '<a href="' . $link . '">' . $output . '</a>';
	}

	public static function edit_link( $text = null, $before = '', $after = '', $id = 0, $class = 'post-edit-link' ) {
		if ( null === $text ) {
			$post_type = self::get_post_type_object();
			$text = sprintf( __( 'Edit %s', 'wp-starter-theme' ), $post_type->labels->singular_name );
		}

		\edit_post_link( $text, $before, $after, $id, $class );
	}

	public static function get_post_type_object( $post_type = null ) {
		if ( null === $post_type ) {
			if ( $post = get_post() ) {
				$post_type = $post->post_type;
			} else {
				$post_type = 'post';
			}
		}

		return \get_post_type_object( $post_type );
	}

	public static function is_multi_categories() {
		return self::is_multi_terms( 'category' );
	}

	public static function is_multi_tags() {
		return self::is_multi_terms( 'post_tag' );
	}

	public static function is_multi_post_formats() {
		return self::is_multi_terms( 'post_format' );
	}

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

	public static function init() {
		add_action( 'save_post', array( __CLASS__, '_clear_is_multi_terms_post_cache' ), 10, 1 );
	}

	private static function _clear_is_multi_terms_post_cache( $post_id ) {
		self::_clear_is_multi_terms_cache( get_object_taxonomies( get_post( $post_id ) ) );
	}

	private static function _clear_is_multi_terms_cache( $taxonomies ) {
		$taxonomies = (array) $taxonomies;

		foreach ( $taxonomies as $taxonomy ) {
			delete_transient( 'wp_starter_theme_is_multi_' . $taxonomy );
		}
	}
}
