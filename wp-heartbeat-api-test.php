<?php
/**
 * Plugin Name: Wp Heartbeat Api Test
 * Plugin URI:  https://welabs.dev
 * Description: Custom plugin by weLabs
 * Version: 0.0.1
 * Author: WeLabs
 * Author URI: https://welabs.dev
 * Text Domain: wp-heartbeat-api-test
 * WC requires at least: 5.0.0
 * Domain Path: /languages/
 * License: GPL2
 */
use WeLabs\WpHeartbeatApiTest\WpHeartbeatApiTest;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WP_HEARTBEAT_API_TEST_FILE' ) ) {
    define( 'WP_HEARTBEAT_API_TEST_FILE', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Load Wp_Heartbeat_Api_Test Plugin when all plugins loaded
 *
 * @return \WeLabs\WpHeartbeatApiTest\WpHeartbeatApiTest
 */
function welabs_wp_heartbeat_api_test() {
    return WpHeartbeatApiTest::init();
}

// Lets Go....
welabs_wp_heartbeat_api_test();
