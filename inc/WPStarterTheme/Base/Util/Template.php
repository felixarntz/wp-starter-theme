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
			$cache = self::_get_cache_key( $slug, $data );

			if ( $cache ) {
				$output = wp_cache_get( $cache, 'templateparts' );
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
			wp_cache_set( $cache, $output, 'templateparts', self::CACHE_DURATION );
		}

		$output = self::_add_output_html_comments( $output, $slug );

		if ( $return ) {
			return $output;
		}

		echo $output;
	}

	public static function init() {
		add_filter( 'the_password_form', array( __CLASS__, '_get_the_password_form' ) );
	}

	public static function _get_the_password_form( $output ) {
		$filename = locate_template( array( 'passwordform.php' ), false, false );

		if ( ! $filename ) {
			return $output;
		}

		$matches = array();
		if ( preg_match( '/id="pwbox-(\d+)"/', $output, $matches ) ) {
			$post = get_post( $matches[1] );

			ob_start();
			self::_load_template( $filename, array( 'post'	=> $post ), false );
			$output = ob_get_clean();
		}

		return $output;
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

	private static function _get_cache_key( $slug, $data = array() ) {
		$cache_args = array( 'slug' => str_replace( 'template-parts/', '', $slug ) );
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
		if ( count( $data ) !== count( $cache_args ) - 1 ) {
			return false;
		}

		$cache_key = '';
		foreach ( $cache_args as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = serialize( $value );
			} elseif ( is_bool( $value ) ) {
				$value = $value ? 'true' : 'false';
			}
			$cache_key .= $key . ':' . $value . ';';
		}
		return $cache_key;
	}
}
