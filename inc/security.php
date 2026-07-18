<?php

// Login brute-force protection: lock out an IP after repeated failed attempts.
define( 'GM_LOGIN_MAX_ATTEMPTS', 5 );
define( 'GM_LOGIN_LOCKOUT_SECONDS', 15 * MINUTE_IN_SECONDS );

function gm_login_client_ip() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
}

// Priority 30 runs AFTER core's wp_authenticate_username_password/email_password
// (both priority 20), which otherwise ignore an incoming WP_Error and overwrite it
// with their own lookup whenever both username and password are non-empty — exactly
// the case for a real brute-force attempt. Running later lets us override the result
// unconditionally instead of being silently discarded by core.
add_filter( 'authenticate', 'gm_check_login_lockout', 30 );
function gm_check_login_lockout( $user ) {
	$ip = gm_login_client_ip();
	if ( get_transient( 'gm_login_lock_' . md5( $ip ) ) ) {
		return new WP_Error( 'gm_locked_out', __( '<strong>Error:</strong> Too many failed login attempts. Please try again in 15 minutes.' ) );
	}
	return $user;
}

add_action( 'wp_login_failed', 'gm_record_login_failure' );
function gm_record_login_failure( $username ) {
	$ip        = gm_login_client_ip();
	$count_key = 'gm_login_fails_' . md5( $ip );
	$attempts  = (int) get_transient( $count_key ) + 1;
	set_transient( $count_key, $attempts, GM_LOGIN_LOCKOUT_SECONDS );

	if ( $attempts >= GM_LOGIN_MAX_ATTEMPTS ) {
		set_transient( 'gm_login_lock_' . md5( $ip ), 1, GM_LOGIN_LOCKOUT_SECONDS );
	}
}

add_action( 'wp_login', 'gm_clear_login_failures' );
function gm_clear_login_failures() {
	$ip = gm_login_client_ip();
	delete_transient( 'gm_login_fails_' . md5( $ip ) );
	delete_transient( 'gm_login_lock_' . md5( $ip ) );
}

// Don't reveal whether a username exists via the login error message
// (but keep the lockout message intact so locked-out users know why).
add_filter( 'login_errors', function ( $error ) {
	if ( strpos( $error, 'Too many failed login attempts' ) !== false ) {
		return $error;
	}
	return __( 'Invalid credentials.' );
} );

// Disable XML-RPC — a common brute-force amplification vector (system.multicall +
// wp.getUsersBlogs), unused by this theme. The xmlrpc_enabled filter alone does NOT
// block the endpoint (system.listMethods etc. ignore it) — clearing the method list
// is what actually neuters it.
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', '__return_empty_array' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
