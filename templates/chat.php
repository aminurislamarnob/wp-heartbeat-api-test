<div id="message_container">
	<h2>Send a Message</h2>
	<input type="hidden" id="recipient_id" value="1"> <!-- Example: Replace with actual recipient ID -->
	<input type="hidden" id="send_message_nonce" value="<?php echo wp_create_nonce( 'send_message_nonce' ); ?>">

	<textarea id="message_input" placeholder="Type your message here"></textarea>
	<button id="send_message_button">Send</button>
	<div id="message_status"></div>

	<h2>Received Messages</h2>
	<ul id="received_messages"></ul>
</div>