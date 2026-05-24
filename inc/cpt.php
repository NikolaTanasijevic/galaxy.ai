<?php

add_action( 'init', 'gm_register_cpt_domain' );
function gm_register_cpt_domain() {
	register_post_type( 'domain', [
		'labels' => [
			'name'               => 'Domains',
			'singular_name'      => 'Domain',
			'add_new_item'       => 'Add New Domain',
			'edit_item'          => 'Edit Domain',
			'search_items'       => 'Search Domains',
			'not_found'          => 'No domains found',
			'not_found_in_trash' => 'No domains in trash',
		],
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => [ 'slug' => 'domains', 'with_front' => false ],
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-admin-site-alt3',
		'supports'     => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
		'menu_position' => 5,
	] );

	register_taxonomy( 'domain_cat', 'domain', [
		'labels' => [
			'name'          => 'Domain Categories',
			'singular_name' => 'Domain Category',
			'edit_item'     => 'Edit Category',
			'add_new_item'  => 'Add New Category',
		],
		'public'            => true,
		'hierarchical'      => false,
		'show_in_rest'      => true,
		'rewrite'           => [ 'slug' => 'category', 'with_front' => false ],
		'show_admin_column' => true,
	] );

	register_post_type( 'domain_bundle', [
		'labels' => [
			'name'          => 'Portfolio Bundles',
			'singular_name' => 'Portfolio Bundle',
			'add_new_item'  => 'Add New Bundle',
			'edit_item'     => 'Edit Bundle',
		],
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => [ 'slug' => 'portfolio', 'with_front' => false ],
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-portfolio',
		'supports'     => [ 'title', 'editor', 'custom-fields' ],
		'menu_position' => 6,
	] );
}

add_action( 'add_meta_boxes', 'gm_register_meta_boxes' );
function gm_register_meta_boxes() {
	add_meta_box( 'gm_domain_details', 'Domain Details', 'gm_domain_details_cb', 'domain', 'normal', 'high' );
	add_meta_box( 'gm_bundle_domains', 'Domains in Bundle', 'gm_bundle_domains_cb', 'domain_bundle', 'normal', 'high' );
}

function gm_domain_details_cb( $post ) {
	wp_nonce_field( 'gm_save_domain', 'gm_domain_nonce' );
	$fields = [
		'gm_price'        => [ 'label' => 'Price (USD, leave empty for "Make Offer")', 'type' => 'number' ],
		'gm_tld'          => [ 'label' => 'TLD (e.g. .com, .ai)', 'type' => 'text' ],
		'gm_listing_url'  => [ 'label' => 'External listing URL (GoDaddy / Afternic / Dan)', 'type' => 'url' ],
		'gm_keywords'     => [ 'label' => 'Keywords (comma-separated)', 'type' => 'text' ],
		'gm_short_desc'   => [ 'label' => 'Short description (shown on card)', 'type' => 'textarea' ],
		'gm_status'       => [ 'label' => 'Status', 'type' => 'select', 'options' => [ 'available' => 'Available', 'sold' => 'Sold', 'on_hold' => 'On Hold' ] ],
		'gm_featured'     => [ 'label' => 'Featured domain', 'type' => 'checkbox' ],
		'gm_logo'         => [ 'label' => 'Domain logo / icon URL (optional, for future branding)', 'type' => 'url' ],
	];
	foreach ( $fields as $key => $field ) {
		$val = get_post_meta( $post->ID, $key, true );
		echo '<p><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $field['label'] ) . '</strong></label><br>';
		if ( $field['type'] === 'textarea' ) {
			echo '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" rows="3" style="width:100%">' . esc_textarea( $val ) . '</textarea>';
		} elseif ( $field['type'] === 'select' ) {
			echo '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">';
			foreach ( $field['options'] as $opt_val => $opt_label ) {
				echo '<option value="' . esc_attr( $opt_val ) . '"' . selected( $val, $opt_val, false ) . '>' . esc_html( $opt_label ) . '</option>';
			}
			echo '</select>';
		} elseif ( $field['type'] === 'checkbox' ) {
			echo '<input type="checkbox" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="1"' . checked( $val, '1', false ) . '>';
		} else {
			echo '<input type="' . esc_attr( $field['type'] ) . '" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" style="width:100%">';
		}
		echo '</p>';
	}
}

function gm_bundle_domains_cb( $post ) {
	wp_nonce_field( 'gm_save_bundle', 'gm_bundle_nonce' );
	$saved = get_post_meta( $post->ID, 'gm_bundle_domain_ids', true ) ?: [];
	$domains = get_posts( [ 'post_type' => 'domain', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ] );
	echo '<p>Select which domains belong to this bundle:</p>';
	echo '<div style="columns:3;column-gap:20px">';
	foreach ( $domains as $domain ) {
		$checked = in_array( $domain->ID, (array) $saved ) ? 'checked' : '';
		echo '<label style="display:block;margin-bottom:6px"><input type="checkbox" name="gm_bundle_domain_ids[]" value="' . esc_attr( $domain->ID ) . '" ' . $checked . '> ' . esc_html( $domain->post_title ) . '</label>';
	}
	echo '</div>';
}

add_action( 'save_post_domain', 'gm_save_domain_meta' );
function gm_save_domain_meta( $post_id ) {
	if ( ! isset( $_POST['gm_domain_nonce'] ) || ! wp_verify_nonce( $_POST['gm_domain_nonce'], 'gm_save_domain' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	$text_fields = [ 'gm_tld', 'gm_listing_url', 'gm_keywords', 'gm_logo' ];
	foreach ( $text_fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ $field ] ) );
		}
	}
	if ( isset( $_POST['gm_price'] ) ) {
		update_post_meta( $post_id, 'gm_price', absint( $_POST['gm_price'] ) );
	}
	if ( isset( $_POST['gm_short_desc'] ) ) {
		update_post_meta( $post_id, 'gm_short_desc', sanitize_textarea_field( $_POST['gm_short_desc'] ) );
	}
	if ( isset( $_POST['gm_status'] ) && in_array( $_POST['gm_status'], [ 'available', 'sold', 'on_hold' ] ) ) {
		update_post_meta( $post_id, 'gm_status', $_POST['gm_status'] );
	}
	update_post_meta( $post_id, 'gm_featured', isset( $_POST['gm_featured'] ) ? '1' : '0' );
}

add_action( 'save_post_domain_bundle', 'gm_save_bundle_meta' );
function gm_save_bundle_meta( $post_id ) {
	if ( ! isset( $_POST['gm_bundle_nonce'] ) || ! wp_verify_nonce( $_POST['gm_bundle_nonce'], 'gm_save_bundle' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	$ids = isset( $_POST['gm_bundle_domain_ids'] ) ? array_map( 'absint', $_POST['gm_bundle_domain_ids'] ) : [];
	update_post_meta( $post_id, 'gm_bundle_domain_ids', $ids );
}
