<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

/**
 * Main theme class.
 *
 * @since 1.0.0
 */
final class Theme {
	/**
	 * Basic theme information.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $info = array();

	/**
	 * The assets instance.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var WPStarterTheme\Base\Assets
	 */
	private $assets;

	/**
	 * The customizer instance.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var WPStarterTheme\Base\Customizer
	 */
	private $customizer;

	/**
	 * The AJAX instance.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var WPStarterTheme\Base\AJAX
	 */
	private $ajax;

	/**
	 * The partials instance.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var WPStarterTheme\Base\Partials
	 */
	private $partials;

	/**
	 * The plugin compatibility instance.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var WPStarterTheme\Base\PluginCompat
	 */
	private $plugin_compat;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		$this->info = wp_get_theme( get_template() );

		$this->assets        = new Assets( $this );
		$this->customizer    = new Customizer( $this );
		$this->ajax          = new AJAX( $this );
		$this->partials      = new Partials( $this );
		$this->plugin_compat = new PluginCompat( $this );
	}

	/**
	 * Adds the necessary hooks to initialize all theme functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function run() {
		Util\Template::init();
		Util\TemplateTags::init();
		Util\Images::init();
		Util\Shortcodes::init();
		Util\BootstrapNavMenu::init();
		Util\BootstrapGallery::init();

		$this->assets->run();
		$this->customizer->run();
		$this->ajax->run();
		$this->plugin_compat->run();

		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 1 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	/**
	 * Initializes early theme functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 */
	public function after_setup_theme() {
		$this->load_textdomain();
		$this->add_editor_style();
		$this->add_image_sizes();
		$this->add_theme_support();
		$this->register_nav_menus();
	}

	/**
	 * Initializes theme sidebars and widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 */
	public function widgets_init() {
		$this->register_sidebars();
	}

	/**
	 * Loads the theme textdomain.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function load_textdomain() {
		load_theme_textdomain( 'wp-starter-theme', Util\Path::get_path( 'languages' ) );
	}

	/**
	 * Adds theme support for various features.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function add_theme_support() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-logo', array(
			'size'        => 'medium',
			'header-text' => array( 'site-title', 'site-description' )
		) );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );
		add_theme_support( 'post-thumbnails' );

		/* Support for plugins. */
		add_theme_support( 'infinite-scroll', array(
			'container'      => 'posts-list',
			'footer'         => false,
			'footer_widgets' => false,
			'wrapper'        => false,
			'render'         => array( $this->partials(), 'render_loop' ),
		) );
		add_theme_support( 'frontkit', array(
			'title'   => '.single-post .post-title',
			'content' => '.single-post .post-content',
		) );
	}

	/**
	 * Sets up image sizes for the theme.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function add_image_sizes() {
		set_post_thumbnail_size( 1280, 720, true );

		//add_image_size( 'extra-large', 1920, 1080, false, true );
	}

	/**
	 * Registers the editor stylesheet.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function add_editor_style() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		add_editor_style( 'assets/dist/css/editor' . $min . '.css' );
	}

	/**
	 * Registers nav menus for the theme.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function register_nav_menus() {
		register_nav_menus( array(
			'primary'	=> __( 'Primary Navigation', 'wp-starter-theme' ),
			'social'	=> __( 'Social Menu', 'wp-starter-theme' ),
		) );
	}

	/**
	 * Registers sidebars for the theme.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function register_sidebars() {
		register_sidebar( array(
			'name'			=> __( 'Primary Sidebar', 'wp-starter-theme' ),
			'id'			=> 'primary',
			'description'	=> __( 'This sidebar is shown beside the main content.', 'wp-starter-theme' ),
			'before_widget'	=> '<aside id="%1$s" class="widget %2$s">',
			'after_widget'	=> '</aside>',
			'before_title'	=> '<h1 class="widget-title">',
			'after_title'	=> '</h1>',
		) );
	}

	/**
	 * Returns theme info.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field Optional. Which field to return. Default empty.
	 * @return string|array|null If $field is empty, an array with all information will be returned.
	 *                           Otherwise the field info will be returned, or null if it does not exist.
	 */
	public function get_info( $field = '' ) {
		if ( ! empty( $field ) ) {
			if ( isset( $this->info[ $field ] ) ) {
				return $this->info[ $field ];
			}
			return null;
		}
		return $this->info;
	}

	/**
	 * Triggers a notice for incorrect usage of theme functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $function Function or method name that has been used incorrectly.
	 * @param string $message  Message to show.
	 * @param string $version  Optional. Theme version in which the message was added. Default null.
	 */
	public function _doing_it_wrong( $function, $message, $version = null ) {
		if ( WP_DEBUG && apply_filters( 'doing_it_wrong_trigger_error', true ) ) {
			$message = sprintf( __( '%s was called <strong>incorrectly</strong>.', 'wp-starter-theme' ), $function ) . ' ' . $message;
			if ( null !== $version ) {
				$message .= ' ' . sprintf( __( 'This message was added in version %s.', 'wp-starter-theme' ), $version );
			}
			trigger_error( $message );
		}
	}

	/**
	 * Returns the assets instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function assets() {
		return $this->assets;
	}

	/**
	 * Returns the customizer instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function customizer() {
		return $this->customizer;
	}

	/**
	 * Returns the AJAX instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function ajax() {
		return $this->ajax;
	}

	/**
	 * Returns the partials instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function partials() {
		return $this->partials;
	}

	/**
	 * Returns the plugin compatibility instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function plugin_compat() {
		return $this->plugin_compat;
	}
}
