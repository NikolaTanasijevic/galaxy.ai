<?php

require_once get_template_directory() . '/inc/cpt.php';
require_once get_template_directory() . '/inc/category-meta.php';
require_once get_template_directory() . '/inc/csv-import.php';
require_once get_template_directory() . '/inc/schema.php';

add_action( 'after_setup_theme', 'gm_theme_setup' );
function gm_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption' ] );
	add_theme_support( 'custom-logo' );

	register_nav_menus( [
		'primary' => 'Primary Navigation',
		'footer'  => 'Footer Navigation',
	] );
}

add_action( 'wp_enqueue_scripts', 'gm_enqueue' );
function gm_enqueue() {
	wp_enqueue_style( 'gm-fonts', 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Inter:wght@300;400;500&display=swap', [], null );
	wp_enqueue_style( 'gm-main', get_template_directory_uri() . '/assets/css/main.css', [ 'gm-fonts' ], '1.0.0' );
	wp_enqueue_script( 'gm-main', get_template_directory_uri() . '/assets/js/main.js', [], '1.0.0', true );

	if ( is_singular( 'domain' ) || is_post_type_archive( 'domain' ) || is_tax( 'domain_cat' ) ) {
		wp_localize_script( 'gm-main', 'gmAjax', [
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'gm_inquiry' ),
		] );
	}
}

add_action( 'init', 'gm_flush_rewrite_once' );
function gm_flush_rewrite_once() {
	if ( get_option( 'gm_flush_rewrite' ) !== '1' ) {
		flush_rewrite_rules();
		update_option( 'gm_flush_rewrite', '1' );
	}
}

// AJAX inquiry form handler
add_action( 'wp_ajax_gm_inquiry', 'gm_handle_inquiry' );
add_action( 'wp_ajax_nopriv_gm_inquiry', 'gm_handle_inquiry' );
function gm_handle_inquiry() {
	check_ajax_referer( 'gm_inquiry', 'nonce' );

	$domain  = sanitize_text_field( $_POST['domain'] ?? '' );
	$name    = sanitize_text_field( $_POST['buyer_name'] ?? '' );
	$email   = sanitize_email( $_POST['buyer_email'] ?? '' );
	$message = sanitize_textarea_field( $_POST['buyer_message'] ?? '' );
	$bundle  = sanitize_text_field( $_POST['bundle'] ?? '' );

	if ( ! $email || ! $name ) {
		wp_send_json_error( 'Please fill in all required fields.' );
	}

	$subject = $bundle
		? "Bundle Inquiry: {$bundle} — Galaxa Media"
		: "Domain Inquiry: {$domain} — Galaxa Media";

	$body = "Name: {$name}\nEmail: {$email}\n\n";
	if ( $bundle ) $body .= "Bundle: {$bundle}\n";
	if ( $domain ) $body .= "Domain: {$domain}\n";
	$body .= "\nMessage:\n{$message}";

	$to      = get_option( 'admin_email' );
	$headers = [ "Reply-To: {$name} <{$email}>", 'Content-Type: text/plain; charset=UTF-8' ];

	wp_mail( $to, $subject, $body, $headers );
	wp_send_json_success( 'Your inquiry has been sent. We\'ll be in touch within 4 business hours.' );
}

// Helper: get domain meta
function gm_domain_meta( $post_id, $key, $default = '' ) {
	$val = get_post_meta( $post_id, $key, true );
	return $val !== '' ? $val : $default;
}

// Helper: format price
function gm_format_price( $post_id ) {
	$price = gm_domain_meta( $post_id, 'gm_price' );
	if ( ! $price ) return '<span class="dc-price make-offer">Make Offer</span>';
	return '<span class="dc-price">$' . number_format( (int) $price ) . '</span>';
}

// Helper: category CSS class
function gm_cat_class( $slug ) {
	$map = [
		'ai'             => 'cat-ai',
		'finance'        => 'cat-finance',
		'travel'         => 'cat-travel',
		'technology'     => 'cat-tech',
		'healthcare'     => 'cat-health',
		'beauty'         => 'cat-beauty',
		'startup-brands' => 'cat-startup',
		'lifestyle'      => 'cat-lifestyle',
		'contractors'    => 'cat-contractors',
	];
	return $map[ $slug ] ?? 'cat-ai';
}
