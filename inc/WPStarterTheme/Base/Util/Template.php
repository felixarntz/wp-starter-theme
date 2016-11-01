<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base\Util;

/**
 * Class to improve template handling.
 *
 * @since 1.0.0
 */
final class Template {
	const CACHE_DURATION = 3600;

	/**
	 * Renders a template.
	 *
	 * This method can be called instead of get_template_part() to support caching and passing data to the template.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string       $slug  The template slug.
	 * @param string|array $data  The template suffix or array of data to pass to the template.
	 * @param bool         $cache Whether to cache the template.
	 * @return string The template output.
	 */
	public static function render( $slug, $data = array(), $cache = false ) {
		if ( is_string( $data ) ) {
			$data = array( 'name' => $data );
		}

		$cache = self::maybe_prevent_caching( $slug, $data, $cache );

		$data = wp_parse_args( $data, array(
			'name'		=> '',
			'return'	=> false,
		) );

		$return = $data['return'];
		unset( $data['return'] );

		if ( $cache ) {
			$cache = self::get_cache_key( $slug, $data );

			if ( $cache ) {
				$output = wp_cache_get( $cache, 'templateparts' );
				if ( false !== $output ) {
					$output = self::add_output_html_comments( $output, $slug, true );
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
			\WPStarterTheme\theme()->_doing_it_wrong( __METHOD__, sprintf( __( 'The template %s does not exist.', 'wp-starter-theme' ), $filename ) );
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

		self::load_template( $filename, $data, $require_once );

		$output = ob_get_clean();

		if ( $cache ) {
			wp_cache_set( $cache, $output, 'templateparts', self::CACHE_DURATION );
		}

		$output = self::add_output_html_comments( $output, $slug );

		if ( $return ) {
			return $output;
		}

		echo $output;
	}

	/**
	 * Adds general filters to support a template for the password form.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		add_filter( 'the_password_form', array( __CLASS__, 'get_the_password_form' ) );
	}

	/**
	 * Tries to find a template for the password form.
	 *
	 * This method is used as callback and should not be called directly.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @internal
	 *
	 * @param string $output Password form output.
	 * @return Modified password form output.
	 */
	public static function get_the_password_form( $output ) {
		$filename = locate_template( array( 'passwordform.php' ), false, false );

		if ( ! $filename ) {
			return $output;
		}

		$matches = array();
		if ( preg_match( '/id="pwbox-(\d+)"/', $output, $matches ) ) {
			$post = get_post( $matches[1] );

			ob_start();
			self::load_template( $filename, array( 'post'	=> $post ), false );
			$output = ob_get_clean();
		}

		return $output;
	}

	/**
	 * Loads a template and passes data to it.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param string $filename     Filename of the template.
	 * @param array  $data         Data to pass to the template.
	 * @param bool   $require_once Optional. Whether to use require_once to load the file. Default false.
	 */
	private static function load_template( $filename, $data = array(), $require_once = true ) {
		extract( $data, EXTR_SKIP );

		if ( $require_once ) {
			require_once $filename;
		} else {
			require $filename;
		}
	}

	/**
	 * Adds HTML comments about caching when WP_DEBUG is enabled.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param string $output The template output.
	 * @param string $slug   The template slug.
	 * @param bool   $cached Whether a cached result was returned.
	 * @return string The modified template output.
	 */
	private static function add_output_html_comments( $output, $slug, $cached = false ) {
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

	/**
	 * Checks whether caching should be handled or not.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param string $slug  The template slug.
	 * @param array  $data  The template data.
	 * @param bool   $cache The original cache value.
	 * @return bool The possibly modified cache value.
	 */
	private static function maybe_prevent_caching( $slug, $data = array(), $cache = false ) {
		if ( ! $cache ) {
			return $cache;
		}

		if ( isset( $_REQUEST['wp_customize'] ) && 'on' === $_REQUEST['wp_customize'] ) {
			return false;
		}

		if ( isset( $_REQUEST['preview'] ) && $_REQUEST['preview'] && isset( $_REQUEST['p'] ) && in_array( $_REQUEST['p'], $data ) ) {
			return false;
		}

		return $cache;
	}

	/**
	 * Returns the unique cache key for a given slug and data.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @param string $slug The template slug.
	 * @param array  $data The template data.
	 * @return string|bool The cache key, or false if impossible to detect a unique cache key.
	 */
	private static function get_cache_key( $slug, $data = array() ) {
		$cache_args = array( 'slug' => str_replace( 'template-parts/', '', $slug ) );
		foreach ( $data as $key => $value ) {
			if ( is_scalar( $value ) || is_array( $value ) ) {
				$cache_args[ $key ] = $value;
			} elseif ( is_object( $value ) && is_callable( array( $value, 'get_ID' ) ) ) {
				$cache_args[ $key ] = call_user_func( array( $value, 'get_ID' ) );
			} else {
				\WPStarterTheme\theme()->_doing_it_wrong( __METHOD__, sprintf( __( 'The value for %1$s is not storable in the cache key for the template %2$s.', 'wp-starter-theme' ), $key, $slug ) );
				break;
			}
		}

		// only use cache if arguments qualify for it
		if ( count( $data ) !== count( $cache_args ) - 1 ) {
			return false;
		}

		$cache_key_parts = array();

		foreach ( $cache_args as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = serialize( $value );
			} elseif ( is_bool( $value ) ) {
				$value = $value ? 'true' : 'false';
			}

			if ( empty( $key ) || empty( $value ) ) {
				continue;
			}

			$cache_key_parts[] = $key . '---' . $value;
		}

		return implode( ':', $cache_key_parts );
	}
}
