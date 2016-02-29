<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

final class ContentNavigation {
	private static $_aligned_helper = false;

	public static function get_the_post_navigation( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'prev_text'				=> '%title',
			'next_text'				=> '%title',
			'in_same_term'			=> false,
			'excluded_terms'		=> '',
			'taxonomy'				=> 'category',
			'screen_reader_text'	=> __( 'Post navigation', 'wp-starter-theme' ),
			'aligned'				=> false,
		) );

		$navigation = '';

		$prev_template = $next_template = '<li>%link</li>';
		if ( $args['aligned'] ) {
			$prev_template = str_replace( '<li>', '<li class="pager-prev">', $prev_template );
			$next_template = str_replace( '<li>', '<li class="pager-next">', $next_template );
		}

		$prev_link = get_previous_post_link( $prev_template, $args['prev_text'], $args['in_same_term'], $args['excluded_terms'], $args['taxonomy'] );
		$next_link = get_next_post_link( $next_template, $args['next_text'], $args['in_same_term'], $args['excluded_terms'], $args['taxonomy'] );

		if ( $prev_link || $next_link ) {
			$navigation = '<ul class="pager">' . $prev_link . $next_link . '</ul>';
			$navigation = _navigation_markup( $navigation, 'post-navigation', $args['screen_reader_text'] );
		}

		return $navigation;
	}

	public static function get_the_posts_navigation( $args = array() ) {
		$navigation = '';

		if ( $GLOBALS['wp_query']->max_num_pages > 1 ) {
			$args = wp_parse_args( $args, array(
				'prev_text'				=> __( 'Older posts', 'wp-starter-theme' ),
				'next_text'				=> __( 'Newer posts', 'wp-starter-theme' ),
				'screen_reader_text'	=> __( 'Posts navigation', 'wp-starter-theme' ),
				'aligned'				=> false,
			) );

			$prev_class = '';
			$next_class = '';
			if ( $args['aligned'] ) {
				$prev_class = ' class="pager-prev"';
				$next_class = ' class="pager-next"';
			}

			$next_link = get_previous_posts_link( $args['next_text'] );
			$prev_link = get_next_posts_link( $args['prev_text'] );

			if ( $prev_link || $next_link ) {
				if ( $prev_link ) {
					$navigation .= '<li' . $prev_class . '>' . $prev_link . '</li>';
				}

				if ( $next_link ) {
					$navigation .= '<li' . $next_class . '>' . $next_link . '</li>';
				}

				$navigation = '<ul class="pager">' . $navigation . '</ul>';
				$navigation = _navigation_markup( $navigation, 'posts-navigation', $args['screen_reader_text'] );
			}
		}

		return $navigation;
	}

	public static function get_the_posts_pagination( $args = array() ) {
		$navigation = '';

		if ( $GLOBALS['wp_query']->max_num_pages > 1 ) {
			$args = wp_parse_args( $args, array(
				'mid_size'				=> 1,
				'prev_text'				=> _x( '&laquo; Previous', 'previous post', 'wp-starter-theme' ),
				'next_text'				=> _x( 'Next &raquo;', 'next post', 'wp-starter-theme' ),
				'screen_reader_text'	=> __( 'Posts navigation', 'wp-starter-theme' ),
				'size'					=> 'default',
				'show_disabled'			=> false,
			) );

			$args['type'] = 'array';

			$links = paginate_links( $args );

			if ( $links ) {
				$link_count = count( $links );

				$pagination_class = 'pagination';
				if ( 'large' == $args['size'] ) {
					$pagination_class .= ' pagination-lg';
				} elseif ( 'small' == $args['size'] ) {
					$pagination_class .= ' pagination-sm';
				}

				$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
				$total = isset( $wp_query->max_num_pages ) ? intval( $wp_query->max_num_pages ) : 1;

				$navigation .= '<ul class="' . $pagination_class . '">';

				if ( $args['show_disabled'] && 1 === $current ) {
					$navigation .= '<li class="page-item disabled"><a href="page-link" href="#">' . $args['prev_text'] . '</a></li>';
				}

				foreach ( $links as $index => $link ) {
					if ( 0 == $index && 0 === strpos( $link, '<a class="prev' ) ) {
						$navigation .= '<li class="page-item">' . str_replace( 'prev page-numbers', 'page-link', $link ) . '</li>';
					} elseif ( $link_count - 1 == $index && 0 === strpos( $link, '<a class="next' ) ) {
						$navigation .= '<li class="page-item">' . str_replace( 'next page-numbers', 'page-link', $link ) . '</li>';
					} else {
						$link = preg_replace( "/(class|href)='(.*)'/U", '$1="$2"', $link );
						if ( 0 === strpos( $link, '<span class="page-numbers current' ) ) {
							$navigation .= '<li class="page-item active">' . str_replace( array( '<span class="page-numbers current">', '</span>' ), array( '<a class="page-link" href="#">', '</a>' ), $link ) . '</li>';
						} elseif ( 0 === strpos( $link, '<span class="page-numbers dots' ) ) {
							$navigation .= '<li class="page-item disabled">' . str_replace( array( '<span class="page-numbers dots">', '</span>' ), array( '<a class="page-link" href="#">', '</a>' ), $link ) . '</li>';
						} else {
							$navigation .= '<li class="page-item">' . str_replace( 'class="page-numbers', 'class="page-link', $link ) . '</li>';
						}
					}
				}

				if ( $args['show_disabled'] && $current === $total ) {
					$navigation .= '<li class="page-item disabled"><a href="page-link" href="#">' . $args['next_text'] . '</a></li>';
				}

				$navigation .= '</ul>';

				$navigation = _navigation_markup( $navigation, 'pagination', $args['screen_reader_text'] );
			}
		}

		return $navigation;
	}

	public static function get_the_comments_navigation( $args = array() ) {
		$navigation = '';

		if ( get_comment_pages_count() > 1 ) {
			$args = wp_parse_args( $args, array(
				'prev_text'				=> __( 'Older comments', 'wp-starter-theme' ),
				'next_text'				=> __( 'Newer comments', 'wp-starter-theme' ),
				'screen_reader_text'	=> __( 'Comments navigation', 'wp-starter-theme' ),
				'aligned'				=> false,
			) );

			$prev_class = '';
			$next_class = '';
			if ( $args['aligned'] ) {
				$prev_class = ' class="pager-prev"';
				$next_class = ' class="pager-next"';
			}

			$prev_link = get_previous_comments_link( $args['prev_text'] );
			$next_link = get_next_comments_link( $args['next_text'] );

			if ( $prev_link || $next_link ) {
				if ( $prev_link ) {
					$navigation .= '<li' . $prev_class . '>' . $prev_link . '</li>';
				}

				if ( $next_link ) {
					$navigation .= '<li' . $next_class . '>' . $next_link . '</li>';
				}

				$navigation = '<ul class="pager">' . $navigation . '</ul>';
				$navigation = _navigation_markup( $navigation, 'comment-navigation', $args['screen_reader_text'] );
			}
		}

		return $navigation;
	}

	public static function get_the_comments_pagination( $args = array() ) {
		$navigation = '';

		if ( $GLOBALS['wp_query']->max_num_pages > 1 ) {
			$args = wp_parse_args( $args, array(
				'prev_text'				=> _x( '&laquo; Previous', 'previous comment', 'wp-starter-theme' ),
				'next_text'				=> _x( 'Next &raquo;', 'next comment', 'wp-starter-theme' ),
				'screen_reader_text'	=> __( 'Comments navigation', 'wp-starter-theme' ),
				'size'					=> 'default',
				'show_disabled'			=> false,
			) );

			$args['type'] = 'array';
			$args['echo'] = false;

			$links = paginate_comments_links( $args );

			if ( $links ) {
				$link_count = count( $links );

				$pagination_class = 'pagination';
				if ( 'large' == $args['size'] ) {
					$pagination_class .= ' pagination-lg';
				} elseif ( 'small' == $args['size'] ) {
					$pagination_class .= ' pagination-sm';
				}

				$current = get_query_var( 'cpage' ) ? intval( get_query_var( 'cpage' ) ) : 1;
				$total = get_comment_pages_count();

				$navigation .= '<ul class="' . $pagination_class . '">';

				if ( $args['show_disabled'] && 1 === $current ) {
					$navigation .= '<li class="page-item disabled"><a href="page-link" href="#">' . $args['prev_text'] . '</a></li>';
				}

				foreach ( $links as $index => $link ) {
					if ( 0 == $index && 0 === strpos( $link, '<a class="prev' ) ) {
						$navigation .= '<li class="page-item">' . str_replace( 'prev page-numbers', 'page-link', $link ) . '</li>';
					} elseif ( $link_count - 1 == $index && 0 === strpos( $link, '<a class="next' ) ) {
						$navigation .= '<li class="page-item">' . str_replace( 'next page-numbers', 'page-link', $link ) . '</li>';
					} else {
						$link = preg_replace( "/(class|href)='(.*)'/U", '$1="$2"', $link );
						if ( 0 === strpos( $link, '<span class="page-numbers current' ) ) {
							$navigation .= '<li class="page-item active">' . str_replace( array( '<span class="page-numbers current">', '</span>' ), array( '<a class="page-link" href="#">', '</a>' ), $link ) . '</li>';
						} elseif ( 0 === strpos( $link, '<span class="page-numbers dots' ) ) {
							$navigation .= '<li class="page-item disabled">' . str_replace( array( '<span class="page-numbers dots">', '</span>' ), array( '<a class="page-link" href="#">', '</a>' ), $link ) . '</li>';
						} else {
							$navigation .= '<li class="page-item">' . str_replace( 'class="page-numbers', 'class="page-link', $link ) . '</li>';
						}
					}
				}

				if ( $args['show_disabled'] && $current === $total ) {
					$navigation .= '<li class="page-item disabled"><a href="page-link" href="#">' . $args['next_text'] . '</a></li>';
				}

				$navigation .= '</ul>';

				$navigation = _navigation_markup( $navigation, 'comments-pagination', $args['screen_reader_text'] );
			}
		}

		return $navigation;
	}

	public static function wp_link_pages( $args = array() ) {
		$echo = ! isset( $args['echo'] ) || $args['echo'];

		$args['echo'] = false;

		if ( ! isset( $args['next_or_number'] ) ) {
			$args['next_or_number'] = 'next';
		}

		if ( isset( $args['aligned'] ) && $args['aligned'] ) {
			self::$_aligned_helper = true;
		} else {
			self::$_aligned_helper = false;
		}

		add_filter( 'wp_link_pages_link', array( __CLASS__, '_link_pages_link' ), 10, 2 );

		$navigation = \wp_link_pages( $args );

		remove_filter( 'wp_link_pages_link', array( __CLASS__, '_link_pages_link' ), 10, 2 );

		if ( $navigation ) {
			$navigation = '<ul class="pager">' . $navigation . '</ul>';

			$screen_reader_text = isset( $args['screen_reader_text'] ) ? $args['screen_reader_text'] : __( 'Post page navigation', 'wp-starter-theme' );
			$navigation = _navigation_markup( $navigation, 'post-page-navigation', $screen_reader_text );
		}

		if ( $echo ) {
			echo $navigation;
		}

		return $navigation;
	}

	public static function _link_pages_link( $link, $index ) {
		global $page;

		if ( $index < $page ) {
			return '<li class="pager-prev">' . $link . '</li>';
		} elseif ( $index > $page ) {
			return '<li class="pager-next">' . $link . '</li>';
		}

		return '<li>' . $link . '</li>';
	}
}
