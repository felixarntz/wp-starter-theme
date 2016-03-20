<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

final class TemplateTags {
	private static $relative_dates = false;
	private static $show_post_date = array( 'post' );
	private static $show_post_modified = array();

	public static function get_the_post_meta( $post = null ) {
		$post = get_post( $post );

		if ( ! $post ) {
			return;
		}

		$output = '<ul class="post-meta post-meta-' . $post->post_type . '">';

		if ( in_array( $post->post_type, self::$show_post_date, true ) ) {
			$output .= '<li class="post-date">' . self::get_the_post_date( $post ) . '</li>';
		}

		if ( in_array( $post->post_type, self::$show_post_modified, true ) ) {
			$output .= '<li class="post-date">' . self::get_the_post_modified_date( $post ) . '</li>';
		}

		if ( post_type_supports( $post->post_type, 'author' ) && is_multi_author() ) {
			$output .= '<li class="post-author">' . get_the_author_posts_link() . '</li>';
		}

		switch ( $post->post_type ) {
			case 'page':
				break;
			case 'post':
				if ( self::is_multi_categories() ) {
					$output .= get_the_category_list( '<li class="post-categories">', ', ', '</li>', $post->ID );
				}
				if ( self::is_multi_tags() ) {
					$output .= get_the_tag_list( '<li class="post-tags">', ', ', '</li>', $post->ID );
				}
				if ( current_theme_supports( 'post-formats' ) && self::is_multi_post_formats() ) {
					$output .= self::get_the_post_format( $post );
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
						$output .= get_the_term_list( $post->ID, $taxonomy, '<li class="' . $class . '">', ', ', '</li>' );
					}
				}
				if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) && self::is_multi_post_formats() ) {
					$output .= self::get_the_post_format( $post );
				}
		}
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
		$transient_name = 'wp_starter_theme_is_multi_' . $taxonomy;

		if ( false === ( $is_multi_terms = get_transient( $transient_name ) ) ) {
			$is_multi_terms = get_terms( array(
				'taxonomy'		=> $taxonomy,
				'fields'		=> 'count',
				'hide_empty'	=> 1,
				'number'		=> 2,
			) );

			$is_multi_terms = intval( $is_multi_terms ) > 1 ? 1 : 0;

			set_transient( $transient_name, $is_multi_terms );
		}

		return (bool) $is_multi_terms;
	}

	public static function init() {
		add_action( 'save_post', array( __CLASS__, '_clear_is_multi_terms_post_cache' ), 10, 1 );
	}

	private static function _clear_is_multi_terms_post_cache( $post_id ) {
		self::_clear_is_multi_terms_cache( get_object_taxonomies( get_post( $post_id ) );
	}

	private static function _clear_is_multi_terms_cache( $taxonomies ) {
		$taxonomies = (array) $taxonomies;

		foreach ( $taxonomies as $taxonomy ) {
			delete_transient( 'wp_starter_theme_is_multi_' . $taxonomy );
		}
	}
}
