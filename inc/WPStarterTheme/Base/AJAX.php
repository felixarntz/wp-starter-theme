<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

/**
 * Class to handle AJAX actions and requests.
 *
 * @since 1.0.0
 */
final class AJAX extends ThemeUtilityBase {
	/**
	 * The prefix for the AJAX actions.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $prefix = '';

	/**
	 * The names of all AJAX actions.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $actions = array();

	/**
	 * The names of all AJAX actions that may run for non-logged-in users.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $actions_nopriv = array();

	/**
	 * Nonces for the AJAX actions.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $nonces = array();

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WPStarterTheme\Base\Theme $theme The theme instance.
	 */
	public function __construct( $theme ) {
		parent::__construct( $theme );

		$this->prefix = 'wp_starter_theme_';

		$this->actions = array();
		$this->actions_nopriv = array();
	}

	/**
	 * Adds the necessary hooks to initialize AJAX functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function run() {
		add_action( 'admin_init', array( $this, 'add_actions' ) );
	}

	/**
	 * Adds hooks for all AJAX actions.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_actions() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return;
		}

		foreach ( $this->actions as $action ) {
			add_action( 'wp_ajax_' . $this->prefix . $action, array( $this, 'request' ) );
			if ( in_array( $action, $this->actions_nopriv, true ) ) {
				add_action( 'wp_ajax_nopriv_' . $this->prefix . $action, array( $this, 'request' ) );
			}
		}
	}

	/**
	 * Handles an AJAX request.
	 *
	 * @since 1.0.0
	 * @access public
	 * @internal
	 */
	public function request() {
		$action = str_replace( $this->prefix, '', $_REQUEST['action'] );

		if ( ! isset( $this->actions[ $action ] ) ) {
			wp_send_json_error( __( 'Invalid action.', 'wp-starter-theme' ) );
		}

		if ( ! isset( $_REQUEST['nonce'] ) ) {
			wp_send_json_error( __( 'Missing nonce.', 'wp-starter-theme' ) );
		}

		if ( ! check_ajax_referer( 'ajax_' . $this->prefix . $action, 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid nonce.', 'wp-starter-theme' ) );
		}

		if ( ! is_callable( array( $this, 'ajax_' . $action ) ) ) {
			wp_send_json_error( __( 'Missing action callback.', 'wp-starter-theme' ) );
		}

		$response = call_user_func( array( $this, 'ajax_' . $action ), $_REQUEST );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( $response->get_error_message() );
		}

		wp_send_json_success( $response );
	}

	/**
	 * Returns the nonce for a given AJAX action.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $action Name of the action.
	 * @return string Nonce for the action.
	 */
	public function get_nonce( $action ) {
		if ( ! isset( $this->nonces[ $action ] ) ) {
			$this->nonces[ $action ] = wp_create_nonce( 'ajax_' . $this->prefix . $action );
		}

		return $this->nonces[ $action ];
	}

	/**
	 * Returns the nonces for all AJAX actions.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $nopriv Optional. Whether to include nonces for non-logged-in user actions. Default false.
	 * @return array Associative array of action names and their nonces.
	 */
	public function get_nonces( $nopriv = false ) {
		$actions = $nopriv ? $this->actions_nopriv : $this->actions;

		$nonces = array();
		foreach ( $actions as $action ) {
			$nonces[ $action ] = $this->get_nonce( $action );
		}

		return $nonces;
	}
}
