<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

if ( 0 > version_compare( phpversion(), '5.3.0' ) ) {
	// load textdomain early and stop execution
	load_theme_textdomain( 'wp-starter-theme', get_template_directory() . '/languages' );

	if ( is_admin() ) {
		if ( ! function_exists( 'show_theme_php_error_message' ) ) {
			function show_theme_php_error_message() {
				if ( ! current_user_can( 'edit_posts' ) ) {
					return;
				}
				?>
				<div class="error">
					<p>
						<strong><?php _e( 'Critical Theme Error:', 'wp-starter-theme' ); ?></strong>
						<?php printf( __( 'The current theme requires PHP version %1$s, but the server is only running version %2$s.', 'wp-starter-theme' ), '5.3.0', phpversion() ); ?>
					</p>
				</div>
				<?php
			}
			add_action( 'admin_notices', 'show_theme_php_error_message' );
		}
	} else {
		wp_die( __( 'The current theme requires a PHP version higher than the one running on the server.', 'wp-starter-theme' ), __( 'PHP version outdated', 'wp-starter-theme' ) );
	}

	return;
}

// load the autoloader and bootstrap some dependencies
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';

	// load the Options Definitely plugin
	require_once dirname( __FILE__ ) . '/vendor/felixarntz/options-definitely/options-definitely.php';

	// load the WP Objects plugin textdomain if it hasn't been loaded yet
	if ( ! is_textdomain_loaded( 'wp-objects' ) ) {
		$locale = get_locale();

		if ( file_exists( dirname( __FILE__ ) . '/vendor/felixarntz/wp-objects/languages/wp-objects-' . $locale . '.mo' ) ) {
			load_textdomain( 'wp-objects', dirname( __FILE__ ) . '/vendor/felixarntz/wp-objects/languages/wp-objects-' . $locale . '.mo' );
		}
	}
}

// run the theme
add_action( 'after_setup_theme', array( call_user_func( 'WPStarterTheme\Base\Theme', 'instance' ), 'run' ) );
