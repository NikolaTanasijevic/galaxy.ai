<?php

add_action( 'wp_head', 'gm_output_schema' );
function gm_output_schema() {
	if ( ! is_singular( 'domain' ) ) return;

	$post    = get_queried_object();
	$price   = get_post_meta( $post->ID, 'gm_price', true );
	$status  = get_post_meta( $post->ID, 'gm_status', true ) ?: 'available';
	$tld     = get_post_meta( $post->ID, 'gm_short_desc', true );

	$avail = $status === 'available' ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut';

	$schema = [
		'@context' => 'https://schema.org',
		'@type'    => 'Product',
		'name'     => get_the_title( $post->ID ),
		'url'      => get_permalink( $post->ID ),
		'description' => wp_strip_all_tags( get_the_excerpt( $post->ID ) ),
		'offers'   => [
			'@type'         => 'Offer',
			'priceCurrency' => 'USD',
			'price'         => $price ? $price : '0',
			'availability'  => $avail,
			'url'           => get_permalink( $post->ID ),
			'seller'        => [
				'@type' => 'Organization',
				'name'  => 'Galaxa Media',
				'url'   => home_url(),
			],
		],
	];

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
