<?php
// Buyer-acknowledgment records for domain/portfolio inquiries (client's Developer Notes).
// Each inquiry is stored as a private post so the client can review it in wp-admin.

// Bump when the Terms of Use page text changes, so records show which version was accepted.
define( 'GM_TERMS_VERSION', '1.0 (2026-09-22)' );

function gm_ack_text() {
	return 'I understand that Galaxa has not represented that this domain is available for my intended use or free of third-party rights. I am responsible for conducting my own trademark, legal, and business due diligence before purchasing or using the domain.';
}

add_action( 'init', 'gm_register_inquiry_cpt' );
function gm_register_inquiry_cpt() {
	register_post_type( 'gm_inquiry', [
		'labels' => [
			'name'          => 'Inquiries',
			'singular_name' => 'Inquiry',
			'edit_item'     => 'Inquiry Record',
			'search_items'  => 'Search Inquiries',
			'not_found'     => 'No inquiries yet',
		],
		'public'          => false,
		'show_ui'         => true,
		'show_in_rest'    => false,
		'menu_icon'       => 'dashicons-email-alt',
		'menu_position'   => 7,
		'supports'        => [ 'title' ],
		'capability_type' => 'post',
		'map_meta_cap'    => true,
		'capabilities'    => [ 'create_posts' => 'do_not_allow' ],
	] );
}

function gm_terms_text_plain() {
	$page = get_page_by_path( 'terms-of-use' );
	if ( ! $page ) return gm_short_disclaimer();
	$text = wp_strip_all_tags( str_replace( [ '</p>', '</h2>' ], "\n\n", $page->post_content ) );
	return trim( preg_replace( "/\n{3,}/", "\n\n", $text ) );
}

// Stores one acknowledged inquiry and returns its reference number.
function gm_record_inquiry( $data ) {
	$ref = 'GLX-' . gmdate( 'Ymd' ) . '-' . strtoupper( wp_generate_password( 4, false ) );
	$id  = wp_insert_post( [
		'post_type'   => 'gm_inquiry',
		'post_title'  => $ref,
		'post_status' => 'private',
	] );
	if ( is_wp_error( $id ) || ! $id ) return $ref;

	$meta = [
		'gm_ref'           => $ref,
		'gm_terms_version' => GM_TERMS_VERSION,
		'gm_timestamp'     => gmdate( 'Y-m-d H:i:s' ) . ' UTC',
		'gm_buyer_name'    => $data['name'],
		'gm_buyer_email'   => $data['email'],
		'gm_domain'        => $data['domain'],
		'gm_bundle'        => $data['bundle'],
		'gm_message'       => $data['message'],
		'gm_ack'           => 'Accepted',
		'gm_ack_text'      => gm_ack_text(),
		'gm_ip'            => gm_login_client_ip(),
	];
	foreach ( $meta as $k => $v ) update_post_meta( $id, $k, $v );
	return $ref;
}

function gm_send_buyer_confirmation( $to, $name, $ref, $subject_item ) {
	$body  = "Hi {$name},\n\n";
	$body .= "Thank you for your inquiry about {$subject_item}. We'll be in touch within 4 business hours.\n\n";
	$body .= "Reference number: {$ref}\n\n";
	$body .= "You confirmed the following acknowledgment:\n\"" . gm_ack_text() . "\"\n\n";
	$body .= "A copy of the applicable Terms of Use (version " . GM_TERMS_VERSION . ") is included below and available at " . home_url( '/terms-of-use/' ) . "\n\n";
	$body .= "----------------------------------------\n" . gm_terms_text_plain() . "\n----------------------------------------\n\n";
	$body .= "Galaxa Media";
	$headers = [ 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . get_option( 'admin_email' ) ];
	return wp_mail( $to, "Your inquiry {$ref} — Galaxa Media", $body, $headers );
}

// ── Admin list columns + read-only detail view ──
add_filter( 'manage_gm_inquiry_posts_columns', function () {
	return [
		'title'    => 'Reference',
		'gm_item'  => 'Domain / Portfolio',
		'gm_buyer' => 'Buyer',
		'gm_terms' => 'Terms version',
		'gm_ack'   => 'Acknowledgment',
		'gm_mail'  => 'Emails',
		'date'     => 'Date',
	];
} );
add_action( 'manage_gm_inquiry_posts_custom_column', function ( $col, $id ) {
	switch ( $col ) {
		case 'gm_item':
			echo esc_html( get_post_meta( $id, 'gm_bundle', true ) ?: get_post_meta( $id, 'gm_domain', true ) );
			break;
		case 'gm_buyer':
			echo esc_html( get_post_meta( $id, 'gm_buyer_name', true ) ) . '<br>' . esc_html( get_post_meta( $id, 'gm_buyer_email', true ) );
			break;
		case 'gm_terms':
			echo esc_html( get_post_meta( $id, 'gm_terms_version', true ) );
			break;
		case 'gm_mail':
			echo 'Admin: ' . esc_html( get_post_meta( $id, 'gm_admin_mail', true ) ?: '-' ) . '<br>Buyer: ' . esc_html( get_post_meta( $id, 'gm_buyer_mail', true ) ?: '-' );
			break;
		case 'gm_ack':
			echo esc_html( get_post_meta( $id, 'gm_ack', true ) );
			break;
	}
}, 10, 2 );

add_action( 'add_meta_boxes_gm_inquiry', function () {
	add_meta_box( 'gm_inquiry_details', 'Inquiry & Acknowledgment Record', function ( $post ) {
		$rows = [
			'Reference'           => 'gm_ref',
			'Timestamp'           => 'gm_timestamp',
			'Terms version'       => 'gm_terms_version',
			'Buyer name'          => 'gm_buyer_name',
			'Buyer email'         => 'gm_buyer_email',
			'Domain'              => 'gm_domain',
			'Portfolio'           => 'gm_bundle',
			'Acknowledgment'      => 'gm_ack',
			'Acknowledgment text' => 'gm_ack_text',
			'IP address'          => 'gm_ip',
			'Admin email'         => 'gm_admin_mail',
			'Buyer email'         => 'gm_buyer_mail',
			'Message'             => 'gm_message',
		];
		echo '<table class="widefat striped"><tbody>';
		foreach ( $rows as $label => $key ) {
			echo '<tr><th style="width:180px">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( get_post_meta( $post->ID, $key, true ) ) ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}, 'gm_inquiry', 'normal', 'high' );
} );

// ── Email delivery status, so a failed send is visible on the record ──
// wp_mail() returns false only when the server refuses to hand the message off. A "sent"
// status that never arrives means the recipient's mail provider dropped it (e.g. no SPF/DKIM).
$GLOBALS['gm_last_mail_error'] = '';
add_action( 'wp_mail_failed', function ( $error ) {
	$GLOBALS['gm_last_mail_error'] = $error->get_error_message();
} );

function gm_log_inquiry_mail( $ref, $who, $sent ) {
	$found = get_posts( [ 'post_type' => 'gm_inquiry', 'title' => $ref, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ] );
	if ( ! $found ) return;
	$status = $sent ? 'Sent' : 'FAILED: ' . ( $GLOBALS['gm_last_mail_error'] ?: 'unknown error' );
	update_post_meta( $found[0], "gm_{$who}_mail", $status );
	$GLOBALS['gm_last_mail_error'] = '';
}
