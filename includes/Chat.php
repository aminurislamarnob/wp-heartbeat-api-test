<?php

namespace WeLabs\WpHeartbeatApiTest;

/**
 * Chat class
 */
class Chat {
	/**
	 * The constructor.
	 */
	public function __construct() {
		add_shortcode( 'welabs_chatbox', array( $this, 'welabs_chatbox_shortcode_callback_func' ) );
		add_action( 'wp_footer', array( $this, 'handle_message_script' ), 100 );

		// ajax data save.
		add_action( 'wp_ajax_send_message', array( $this, 'send_message_to_database' ) );
		add_action( 'wp_ajax_nopriv_send_message', array( $this, 'send_message_to_database' ) );

		// Heartbeat received.
		add_filter( 'heartbeat_received', array( $this, 'handle_heartbeat_requests' ), 10, 2 );
		add_filter( 'heartbeat_nopriv_received', array( $this, 'handle_heartbeat_requests' ), 10, 3 );
		add_filter( 'heartbeat_settings', array( $this, 'my_wp_heartbeat_settings' ), 100 );
	}

	/**
	 * Set server side interval duration
	 *
	 * @param array $settings
	 * @return array
	 */
	public function my_wp_heartbeat_settings( $settings ) {
		$settings['interval'] = 15; // Anything between 15-120.
		error_log( print_r( $settings, true ) );
		return $settings;
	}

	/**
	 * Chatbox markup shortcode
	 *
	 * @return void
	 */
	public function welabs_chatbox_shortcode_callback_func() {
		ob_start();
		include WP_HEARTBEAT_API_TEST_TEMPLATE_DIR . '/chat.php';
		return ob_get_clean();
	}

	/**
	 * Add message script on footer
	 *
	 * @return void
	 */
	public function handle_message_script() {
		?>
		<script>
			jQuery(function ($) {
				wp.heartbeat.interval( 'fast' );
				var message_container = $( '#message_container' );
				if(0 !== message_container.length){
					$(document).on('heartbeat-send', function ( event, data ) {
						data.action = 'send_message'
					});
				}


				jQuery(document).ready(function($) {
					// Send message using Heartbeat API when the "Send" button is clicked
					$('#send_message_button').on('click', function(e) {
						e.preventDefault(); 
						// Get message content from input field
						var messageContent = $('#message_input').val();

						// Ensure message content is not empty
						if (messageContent.trim() === '') {
							alert('Please enter a message.');
							return;
						}

						// Get recipient ID from hidden input field
						var recipientId = $('#recipient_id').val();

						$.ajax({
							type : "post",
							dataType : "json",
							url : '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							data : {
								action: "send_message", 
								nonce : $('#send_message_nonce').val(), 
								message: messageContent,
								recipient_id: recipientId
							},
							success: function( response ) {
								console.log(response);
							}
						});
					});

					// Handle response from server
					$(document).on('heartbeat-tick', function(e, data) {
						if (data['wp_send_message_response']) {
							// Display success/error message
							//Append the message to the html
							console.log(data);
							$('#message_status').text(data['wp_send_message_response']['message']);
						}
					});
				});
			});

		</script>
		<?php
	}


	public function send_message_to_database() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'send_message_nonce' ) ) {
			exit( 'Wrong place' );
		}

		// Get message data from AJAX request.
		$message_content = sanitize_text_field( $_POST['message'] );
		$sender_id       = get_current_user_id();
		$recipient_id    = sanitize_text_field( $_POST['recipient_id'] );

		// Save message to database.
		$message_id = $this->save_message( $sender_id, $recipient_id, $message_content );

		// Prepare response.
		if ( $message_id ) {
			wp_send_json_success( 'Message sent successfully!' );
		} else {
			wp_send_json_error( 'Failed to send message.' );
		}
	}

	/**
	 * Handle heartbeat request.
	 *
	 * @param array $response
	 * @param array $data
	 * @return array
	 */
	public function handle_heartbeat_requests( $response, $data ) {
		error_log( 'litchen heartbeat req' );
		if ( $data['action'] === 'send_message' ) {
			// Fetch data from database and send to the response. Which will send to the client side. You will get the response inside jquery 'heartbeat-tick'.
			$data['message']                      = 'Database response';
			$response['wp_send_message_response'] = $data;
		}

		return $response;
	}

	/**
	 * Save message to db table
	 *
	 * @param [type] $sender_id
	 * @param [type] $recipient_id
	 * @param [type] $message_content
	 * @return void
	 */
	public function save_message( $sender_id, $recipient_id, $message_content ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'messages';

		$wpdb->insert(
			$table_name,
			array(
				'sender_id'    => $sender_id,
				'recipient_id' => $recipient_id,
				'message'      => $message_content,
				'timestamp'    => current_time( 'mysql', true ),
			)
		);

		return $wpdb->insert_id;
	}
}
