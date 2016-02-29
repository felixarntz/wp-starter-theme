<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

final class Template {
	const CACHE_DURATION = 3600;

	public static function render( $slug, $data = array(), $cache = false ) {
		if ( is_string( $data ) ) {
			$data = array( 'name' => $data );
		}

		$data = wp_parse_args( $data, array(
			'name'		=> '',
			'return'	=> false,
		) );

		$return = $data['return'];
		unset( $data['return'] );

		if ( $cache ) {
			$cache_args = array();
			foreach ( $data as $key => $value ) {
				if ( is_scalar( $value ) || is_array( $value ) ) {
					$cache_args[ $key ] = $value;
				} elseif ( is_object( $value ) && is_callable( array( $value, 'get_ID' ) ) ) {
					$cache_args[ $key ] = call_user_func( array( $value, 'get_ID' ) );
				} else {
					WPStarterTheme\Base\Theme::_doing_it_wrong( __METHOD__, sprintf( __( 'The value for %1$s is not storable in the cache key for the template %2$s.', 'wp-starter-theme' ), $key, $slug ) );
					break;
				}
			}

			// only use cache if arguments qualify for it
			if ( count( $data ) !== count( $cache_args ) ) {
				$cache = false;
			} else {
				$cache = serialize( $cache_args );
				$output = wp_cache_get( $slug, $cache );
				if ( false !== $output ) {
					$output = self::_add_output_html_comments( $output, $slug, true );
					if ( $return ) {
						return $output;
					}
					echo $output;
					return;
				}
			}
		}

		$templates = array();
		if ( ! empty( $data['name'] ) ) {
			$templates[] = $slug . '-' . $data['name'] . '.php';
		}
		$templates[] = $slug . '.php';

		$filename = locate_template( $templates, false, false );
		if ( ! $filename ) {
			WPStarterTheme\Base\Theme::_doing_it_wrong( __METHOD__, sprintf( __( 'The template %s does not exist.', 'wp-starter-theme' ), $filename ) );
			return;
		}

		ob_start();

		$require_once = true;

		switch ( $slug ) {
			case 'header':
				do_action( 'get_header', $data['name'] );
				break;
			case 'footer':
				do_action( 'get_footer', $data['name'] );
				break;
			case 'sidebar':
				do_action( 'get_sidebar', $data['name'] );
				break;
			default:
				do_action( 'get_template_part_' . $slug, $slug, $data['name'] );
				$require_once = false;
		}

		self::_load_template( $filename, $data, $require_once );

		$output = ob_get_clean();

		if ( $cache ) {
			wp_cache_set( $slug, $output, $cache, self::CACHE_DURATION );
		}

		$output = self::_add_output_html_comments( $output, $slug );

		if ( $return ) {
			return $output;
		}

		echo $output;
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

	private static function _load_template( $filename, $data = array(), $require_once = true ) {
		extract( $data, EXTR_SKIP );

		if ( $require_once ) {
			require_once $filename;
		} else {
			require $filename;
		}
	}

	private static function _add_output_html_comments( $output, $slug, $cached = false ) {
		if ( ! WP_DEBUG ) {
			return $output;
		}

		if ( $cached ) {
			$start = sprintf( __( 'Start Template %s (cached)', 'wp-starter-theme' ), $slug );
			$end = sprintf( __( 'End Template %s (cached)', 'wp-starter-theme' ), $slug );
		} else {
			$start = sprintf( __( 'Start Template %s', 'wp-starter-theme' ), $slug );
			$end = sprintf( __( 'End Template %s', 'wp-starter-theme' ), $slug );
		}
		return '<!-- ' . $start . ' -->' . "\n" . $output . '<!-- ' . $end . ' -->' . "\n";
	}
}
