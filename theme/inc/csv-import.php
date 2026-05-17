<?php

add_action( 'admin_menu', 'gm_csv_import_menu' );
function gm_csv_import_menu() {
	add_submenu_page(
		'edit.php?post_type=domain',
		'CSV Import',
		'CSV Import',
		'manage_options',
		'gm-csv-import',
		'gm_csv_import_page'
	);
}

function gm_csv_import_page() {
	$result = null;
	if ( isset( $_POST['gm_import_nonce'] ) && wp_verify_nonce( $_POST['gm_import_nonce'], 'gm_csv_import' ) ) {
		if ( ! empty( $_FILES['gm_csv']['tmp_name'] ) ) {
			$result = gm_process_csv( $_FILES['gm_csv']['tmp_name'] );
		}
	}
	?>
	<div class="wrap">
		<h1>Domain CSV Import</h1>
		<?php if ( $result ) : ?>
			<div class="notice notice-success"><p><?php echo esc_html( $result ); ?></p></div>
		<?php endif; ?>
		<p>Upload a CSV file with the following columns (in order):</p>
		<code>domain, category_slug, tld, price, listing_url, keywords, short_description, status, featured</code>
		<ul style="margin:12px 0 20px 20px;list-style:disc">
			<li><strong>domain</strong> — full domain name e.g. <em>neural.ai</em></li>
			<li><strong>category_slug</strong> — one of: ai, travel, finance, contractors, healthcare, beauty, startup-brands, technology, lifestyle</li>
			<li><strong>tld</strong> — e.g. .ai, .com, .io</li>
			<li><strong>price</strong> — number in USD, leave empty for "Make Offer"</li>
			<li><strong>listing_url</strong> — optional GoDaddy/Afternic/Dan URL</li>
			<li><strong>keywords</strong> — comma-separated</li>
			<li><strong>short_description</strong> — shown on domain card (1-2 sentences)</li>
			<li><strong>status</strong> — available | sold | on_hold (defaults to available)</li>
			<li><strong>featured</strong> — 1 for featured, 0 or empty otherwise</li>
		</ul>
		<p>If a domain with the same title already exists it will be <strong>updated</strong>, not duplicated.</p>
		<form method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'gm_csv_import', 'gm_import_nonce' ); ?>
			<input type="file" name="gm_csv" accept=".csv" required style="margin-bottom:12px;display:block">
			<?php submit_button( 'Import CSV', 'primary', 'submit', false ); ?>
		</form>
	</div>
	<?php
}

function gm_process_csv( $file ) {
	$handle = fopen( $file, 'r' );
	if ( ! $handle ) return 'Could not open file.';

	$created = 0;
	$updated = 0;
	$row     = 0;

	while ( ( $data = fgetcsv( $handle ) ) !== false ) {
		$row++;
		if ( $row === 1 && strtolower( trim( $data[0] ) ) === 'domain' ) continue; // skip header

		[ $domain, $cat_slug, $tld, $price, $listing_url, $keywords, $short_desc, $status, $featured ] = array_pad( $data, 9, '' );

		$domain    = sanitize_text_field( trim( $domain ) );
		$cat_slug  = sanitize_title( trim( $cat_slug ) );
		$tld       = sanitize_text_field( trim( $tld ) );
		$price     = absint( trim( $price ) );
		$listing_url = esc_url_raw( trim( $listing_url ) );
		$keywords  = sanitize_text_field( trim( $keywords ) );
		$short_desc = sanitize_textarea_field( trim( $short_desc ) );
		$status    = in_array( trim( $status ), [ 'available', 'sold', 'on_hold' ] ) ? trim( $status ) : 'available';
		$featured  = trim( $featured ) === '1' ? '1' : '0';

		if ( empty( $domain ) ) continue;

		$existing = get_posts( [
			'post_type'      => 'domain',
			'title'          => $domain,
			'posts_per_page' => 1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		] );

		if ( $existing ) {
			$post_id = $existing[0];
			wp_update_post( [ 'ID' => $post_id, 'post_status' => 'publish' ] );
			$updated++;
		} else {
			$post_id = wp_insert_post( [
				'post_type'   => 'domain',
				'post_title'  => $domain,
				'post_status' => 'publish',
			] );
			$created++;
		}

		if ( is_wp_error( $post_id ) ) continue;

		update_post_meta( $post_id, 'gm_tld', $tld );
		update_post_meta( $post_id, 'gm_price', $price );
		update_post_meta( $post_id, 'gm_listing_url', $listing_url );
		update_post_meta( $post_id, 'gm_keywords', $keywords );
		update_post_meta( $post_id, 'gm_short_desc', $short_desc );
		update_post_meta( $post_id, 'gm_status', $status );
		update_post_meta( $post_id, 'gm_featured', $featured );

		if ( $cat_slug ) {
			$term = get_term_by( 'slug', $cat_slug, 'domain_cat' );
			if ( $term ) {
				wp_set_post_terms( $post_id, [ $term->term_id ], 'domain_cat' );
			}
		}
	}

	fclose( $handle );
	return "Import complete: {$created} created, {$updated} updated.";
}
