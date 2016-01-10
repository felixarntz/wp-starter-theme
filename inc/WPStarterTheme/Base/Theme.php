<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

final class Theme {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private $info = array();

	private function __construct() {
		$this->info = wp_get_theme();
	}

	public function run() {
		Util\Images::init();
		Util\NavMenu::init();
		Assets::instance()->run();

		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	public function after_setup_theme() {
		$this->load_textdomain();
		$this->add_theme_support();
		$this->add_editor_style();
		$this->add_image_sizes();
		$this->register_nav_menus();
	}

	public function widgets_init() {
		$this->register_sidebars();
	}

	private function load_textdomain() {
		load_theme_textdomain( 'wp-starter-theme', Util\Path::get_path( 'languages' ) );
	}

	private function add_theme_support() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );
		add_theme_support( 'post-thumbnails' );
	}

	private function add_image_sizes() {
		set_post_thumbnail_size( 1280, 720, true );

		//add_image_size( 'extra-large', 1920, 1080, false, true );
	}

	private function add_editor_style() {
		//TODO
		/*$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		add_editor_style( 'assets/dist/css/editor' . $min . '.css' );*/
	}

	private function register_nav_menus() {
		register_nav_menus( array(
			'primary'	=> __( 'Primary Navigation', 'wp-starter-theme' ),
		) );
	}

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

	public function get_info( $field = '' ) {
		if ( ! empty( $field ) ) {
			if ( isset( $this->info[ $field ] ) ) {
				return $this->info[ $field ];
			}
			return null;
		}
		return $this->info;
	}
}

require_once dirname( dirname( __FILE__ ) ) . '/functions.php';
