<?php

namespace WeLabs\WpHeartbeatApiTest;

/**
 * WpHeartbeatApiTest class
 *
 * @class WpHeartbeatApiTest The class that holds the entire WpHeartbeatApiTest plugin
 */
final class WpHeartbeatApiTest {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $version = '0.0.1';

	/**
	 * Instance of self
	 *
	 * @var WpHeartbeatApiTest
	 */
	private static $instance = null;

	/**
	 * Holds various class instances
	 *
	 * @since 2.6.10
	 *
	 * @var array
	 */
	private $container = array();

	/**
	 * Plugin dependencies
	 *
	 * @since 2.6.10
	 *
	 * @var array
	 */
	private const WP_HEARTBEAT_API_TEST_DEPENEDENCIES = array(
		'plugins'   => array(
			// 'woocommerce/woocommerce.php',
			// 'dokan-lite/dokan.php',
			// 'dokan-pro/dokan-pro.php'
		),
		'classes'   => array(
			// 'Woocommerce',
			// 'WeDevs_Dokan',
			// 'Dokan_Pro'
		),
		'functions' => array(
			// 'dokan_admin_menu_position'
		),
	);

	/**
	 * Constructor for the WpHeartbeatApiTest class
	 *
	 * Sets up all the appropriate hooks and actions
	 * within our plugin.
	 */
	private function __construct() {
		$this->define_constants();

		register_activation_hook( WP_HEARTBEAT_API_TEST_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( WP_HEARTBEAT_API_TEST_FILE, array( $this, 'deactivate' ) );

		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		add_action( 'woocommerce_flush_rewrite_rules', array( $this, 'flush_rewrite_rules' ) );
	}

	/**
	 * Initializes the WpHeartbeatApiTest() class
	 *
	 * Checks for an existing WpHeartbeatApiTest instance
	 * and if it doesn't find one then create a new one.
	 *
	 * @return WpHeartbeatApiTest
	 */
	public static function init() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Magic getter to bypass referencing objects
	 *
	 * @since 2.6.10
	 *
	 * @param string $prop
	 *
	 * @return Class Instance
	 */
	public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->container ) ) {
			return $this->container[ $prop ];
		}
	}

	/**
	 * Placeholder for activation function
	 *
	 * Nothing is being called here yet.
	 */
	public function activate() {
		// Check wp_heartbeat_api_test dependency plugins.
		if ( ! $this->check_dependencies() ) {
			wp_die( $this->get_dependency_message() );
		}

		// Rewrite rules during wp_heartbeat_api_test activation.
		if ( $this->has_woocommerce() ) {
			$this->flush_rewrite_rules();
		}

		// Create messages table.
		$this->create_messages_database_table();
	}

	/**
	 * Create messages table on activation
	 *
	 * @return void
	 */
	private function create_messages_database_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . 'messages';

		$sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            sender_id mediumint(9) NOT NULL,
            recipient_id mediumint(9) NOT NULL,
            message text NOT NULL,
            timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Flush rewrite rules after wp_heartbeat_api_test is activated or woocommerce is activated
	 *
	 * @since 3.2.8
	 */
	public function flush_rewrite_rules() {
		// fix rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Placeholder for deactivation function
	 *
	 * Nothing being called here yet.
	 */
	public function deactivate() {     }

	/**
	 * Define all constants
	 *
	 * @return void
	 */
	public function define_constants() {
		$this->define( 'WP_HEARTBEAT_API_TEST_PLUGIN_VERSION', $this->version );
		$this->define( 'WP_HEARTBEAT_API_TEST_DIR', dirname( WP_HEARTBEAT_API_TEST_FILE ) );
		$this->define( 'WP_HEARTBEAT_API_TEST_INC_DIR', WP_HEARTBEAT_API_TEST_DIR . '/includes' );
		$this->define( 'WP_HEARTBEAT_API_TEST_TEMPLATE_DIR', WP_HEARTBEAT_API_TEST_DIR . '/templates' );
		$this->define( 'WP_HEARTBEAT_API_TEST_PLUGIN_ASSET', plugins_url( 'assets', WP_HEARTBEAT_API_TEST_FILE ) );

		// give a way to turn off loading styles and scripts from parent theme
		$this->define( 'WP_HEARTBEAT_API_TEST_LOAD_STYLE', true );
		$this->define( 'WP_HEARTBEAT_API_TEST_LOAD_SCRIPTS', true );
	}

	/**
	 * Define constant if not already defined
	 *
	 * @param string      $name
	 * @param string|bool $value
	 *
	 * @return void
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Load the plugin after WP User Frontend is loaded
	 *
	 * @return void
	 */
	public function init_plugin() {
		// Check wp_heartbeat_api_test dependency plugins
		if ( ! $this->check_dependencies() ) {
			add_action( 'admin_notices', array( $this, 'admin_error_notice_for_dependency_missing' ) );
			return;
		}

		$this->includes();
		$this->init_hooks();

		do_action( 'wp_heartbeat_api_test_loaded' );
	}

	/**
	 * Initialize the actions
	 *
	 * @return void
	 */
	public function init_hooks() {
		// initialize the classes
		add_action( 'init', array( $this, 'init_classes' ), 4 );
		add_action( 'plugins_loaded', array( $this, 'after_plugins_loaded' ) );
	}

	/**
	 * Include all the required files
	 *
	 * @return void
	 */
	public function includes() {
		// include_once STUB_PLUGIN_DIR . '/functions.php';
	}

	/**
	 * Init all the classes
	 *
	 * @return void
	 */
	public function init_classes() {
		$this->container['scripts'] = new Assets();
		$this->container['chat']    = new Chat();
	}

	/**
	 * Executed after all plugins are loaded
	 *
	 * At this point wp_heartbeat_api_test Pro is loaded
	 *
	 * @since 2.8.7
	 *
	 * @return void
	 */
	public function after_plugins_loaded() {
		// Initiate background processes and other tasks
	}

	/**
	 * Check whether woocommerce is installed and active
	 *
	 * @since 2.9.16
	 *
	 * @return bool
	 */
	public function has_woocommerce() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Check whether woocommerce is installed
	 *
	 * @since 3.2.8
	 *
	 * @return bool
	 */
	public function is_woocommerce_installed() {
		return in_array( 'woocommerce/woocommerce.php', array_keys( get_plugins() ), true );
	}

	/**
	 * Check plugin dependencies
	 *
	 * @return boolean
	 */
	public function check_dependencies() {
		if ( array_key_exists( 'plugins', self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES ) && ! empty( self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES['plugins'] ) ) {
			for ( $plugin_counter = 0; $plugin_counter < count( self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES['plugins'] ); $plugin_counter++ ) {
				if ( ! is_plugin_active( self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES['plugins'][ $plugin_counter ] ) ) {
					return false;
				}
			}
		} elseif ( array_key_exists( 'classes', self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES ) && ! empty( self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES['classes'] ) ) {
			for ( $class_counter = 0; $class_counter < count( self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES['classes'] ); $class_counter++ ) {
				if ( ! class_exists( self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES['classes'][ $class_counter ] ) ) {
					return false;
				}
			}
		} elseif ( array_key_exists( 'functions', self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES ) && ! empty( self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES['functions'] ) ) {
			for ( $func_counter = 0; $func_counter < count( self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES['functions'] ); $func_counter++ ) {
				if ( ! function_exists( self::WP_HEARTBEAT_API_TEST_DEPENEDENCIES['functions'][ $func_counter ] ) ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Dependency error message
	 *
	 * @return void
	 */
	protected function get_dependency_message() {
		return __( 'Wp Heartbeat Api Test plugin is enabled but not effective. It requires dependency plugins to work.', 'wp-heartbeat-api-test' );
	}

	/**
	 * Admin error notice for missing dependency plugins
	 *
	 * @return void
	 */
	public function admin_error_notice_for_dependency_missing() {
		$class = 'notice notice-error';
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $this->get_dependency_message() ) );
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', WP_HEARTBEAT_API_TEST_FILE ) );
	}

	/**
	 * Get the template file path to require or include.
	 *
	 * @param string $name
	 * @return string
	 */
	public function get_template( $name ) {
		$template = untrailingslashit( WP_HEARTBEAT_API_TEST_TEMPLATE_DIR ) . '/' . untrailingslashit( $name );

		return apply_filters( 'wp-heartbeat-api-test_template', $template, $name );
	}
}
