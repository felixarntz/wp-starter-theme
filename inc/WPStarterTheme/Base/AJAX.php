<?php
/**
 * @package WPStarterTheme
 * @version 1.0.0
 */

namespace WPStarterTheme\Base;

final class AJAX {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private $prefix = '';

	private $actions = array();
	private $actions_nopriv = array();

	private $nonces = array();

	private function __construct() {
		$this->prefix = str_replace( '-', '_', 'wp-starter-theme' ) . '_';

		$this->actions = array();
		$this->actions_nopriv = array();
	}

	public function run() {
		add_action( 'admin_init', array( $this, 'add_actions' ) );
	}

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

	public function get_nonce( $action ) {
		if ( ! isset( $this->nonces[ $action ] ) ) {
			$this->nonces[ $action ] = wp_create_nonce( 'ajax_' . $this->prefix . $action );
		}

		return $this->nonces[ $action ];
	}

	public function get_nonces( $nopriv = false ) {
		$actions = $nopriv ? $this->actions_nopriv : $this->actions;

		$nonces = array();
		foreach ( $actions as $action ) {
			$nonces[ $action ] = $this->get_nonce( $action );
		}

		return $nonces;
	}
}
