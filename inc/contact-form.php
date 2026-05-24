<?php

add_shortcode( 'gm_contact_form', 'gm_contact_form_shortcode' );
function gm_contact_form_shortcode() {
	ob_start();
	?>
	<div class="gm-contact-form-wrap">
		<div class="form-success" id="contactFormSuccess"></div>
		<form id="contactForm" novalidate>
			<?php wp_nonce_field( 'gm_contact', 'gm_contact_nonce' ); ?>
			<div class="form-row">
				<div class="form-field">
					<label for="contact_name">Your Name *</label>
					<input type="text" id="contact_name" name="contact_name" required placeholder="Jane Smith">
				</div>
				<div class="form-field">
					<label for="contact_email">Email Address *</label>
					<input type="email" id="contact_email" name="contact_email" required placeholder="jane@company.com">
				</div>
			</div>
			<div class="form-field">
				<label for="contact_subject">Subject</label>
				<input type="text" id="contact_subject" name="contact_subject" placeholder="Domain inquiry, partnership, general question...">
			</div>
			<div class="form-field">
				<label for="contact_message">Message *</label>
				<textarea id="contact_message" name="contact_message" rows="6" required placeholder="Tell us how we can help..."></textarea>
			</div>
			<button type="submit" class="btn-grad" style="border:none;cursor:pointer;font-family:var(--font-b)">Send Message</button>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

add_action( 'wp_ajax_gm_contact', 'gm_handle_contact' );
add_action( 'wp_ajax_nopriv_gm_contact', 'gm_handle_contact' );
function gm_handle_contact() {
	if ( ! isset( $_POST['gm_contact_nonce'] ) || ! wp_verify_nonce( $_POST['gm_contact_nonce'], 'gm_contact' ) ) {
		wp_send_json_error( 'Invalid request.' );
	}

	$name    = sanitize_text_field( $_POST['contact_name'] ?? '' );
	$email   = sanitize_email( $_POST['contact_email'] ?? '' );
	$subject = sanitize_text_field( $_POST['contact_subject'] ?? 'General Inquiry' );
	$message = sanitize_textarea_field( $_POST['contact_message'] ?? '' );

	if ( ! $name || ! $email || ! $message ) {
		wp_send_json_error( 'Please fill in all required fields.' );
	}

	$to      = get_option( 'admin_email' );
	$headers = [ "Reply-To: {$name} <{$email}>", 'Content-Type: text/plain; charset=UTF-8' ];
	$body    = "Name: {$name}\nEmail: {$email}\nSubject: {$subject}\n\nMessage:\n{$message}";

	wp_mail( $to, "Galaxa Media Contact: {$subject}", $body, $headers );
	wp_send_json_success( "Thanks {$name}, your message has been sent. We'll get back to you within 1 business day." );
}
