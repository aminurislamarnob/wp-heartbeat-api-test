<?php

namespace WeLabs\WpHeartbeatApiTest;

class Assets {
	/**
	 * The constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_all_scripts' ), 10 );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 10 );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ) );
		}
	}

	/**
	 * Register all Dokan scripts and styles.
	 *
	 * @return void
	 */
	public function register_all_scripts() {
		$this->register_styles();
		$this->register_scripts();
	}

	/**
	 * Register scripts.
	 *
	 * @param array $scripts
	 *
	 * @return void
	 */
	public function register_scripts() {
		$admin_script    = WP_HEARTBEAT_API_TEST_PLUGIN_ASSET . '/admin/script.js';
		$frontend_script = WP_HEARTBEAT_API_TEST_PLUGIN_ASSET . '/frontend/script.js';

		wp_register_script( 'wp_heartbeat_api_test_admin_script', $admin_script, array(), filemtime( WP_HEARTBEAT_API_TEST_DIR . '/assets/admin/script.js' ), true );
		wp_register_script( 'wp_heartbeat_api_test_script', $frontend_script, array(), filemtime( WP_HEARTBEAT_API_TEST_DIR . '/assets/frontend/script.js' ), true );
	}

	/**
	 * Register styles.
	 *
	 * @return void
	 */
	public function register_styles() {
		$admin_style    = WP_HEARTBEAT_API_TEST_PLUGIN_ASSET . '/admin/style.css';
		$frontend_style = WP_HEARTBEAT_API_TEST_PLUGIN_ASSET . '/frontend/style.css';

		wp_register_style( 'wp_heartbeat_api_test_admin_style', $admin_style, array(), filemtime( WP_HEARTBEAT_API_TEST_DIR . '/assets/admin/style.css' ) );
		wp_register_style( 'wp_heartbeat_api_test_style', $frontend_style, array(), filemtime( WP_HEARTBEAT_API_TEST_DIR . '/assets/frontend/style.css' ) );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'wp_heartbeat_api_test_admin_script' );
		wp_localize_script(
			'wp_heartbeat_api_test_admin_script',
			'Wp_Heartbeat_Api_Test_Admin',
			array()
		);
	}

	/**
	 * Enqueue front-end scripts.
	 *
	 * @return void
	 */
	public function enqueue_front_scripts() {
		wp_enqueue_script( 'wp_heartbeat_api_test_script' );
		wp_enqueue_script( 'heartbeat' );

		wp_localize_script(
			'wp_heartbeat_api_test_script',
			'Wp_Heartbeat_Api_Test',
			array()
		);
	}
}
