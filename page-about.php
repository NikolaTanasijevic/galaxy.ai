<?php get_header(); the_post();

$raw = get_the_content();

preg_match( '/<h1>(.*?)<\/h1>/s', $raw, $h1m );
$hero_title = $h1m[1] ?? get_the_title();
$hero_title = preg_replace(
	'/Galaxa Media/',
	'<span class="gradient-text">Galaxa Media</span>',
	$hero_title,
	1
);

preg_match( '/<\/h1>\s*<p>(.*?)<\/p>/s', $raw, $pm );
$hero_intro = $pm[1] ?? '';

preg_match_all( '/<h2>(.*?)<\/h2>(.*?)(?=<h2>|$)/s', $raw, $sections, PREG_SET_ORDER );

$icon_paths = [
	'target' => '<circle cx="12" cy="12" r="9"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3"/><circle cx="12" cy="12" r="2"/>',
	'person' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>',
	'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
];
?>

<section class="about-hero">
	<div class="about-hero-inner">
		<div class="section-eyebrow">About Us</div>
		<h1><?php echo wp_kses_post( $hero_title ); ?></h1>
		<?php if ( $hero_intro ) : ?><p><?php echo wp_kses_post( $hero_intro ); ?></p><?php endif; ?>
	</div>
</section>

<section class="about-sections">
	<div class="about-sections-inner">
	<?php
	$i = 0;
	foreach ( $sections as $sec ) :
		$title = trim( strip_tags( $sec[1] ) );
		$body  = trim( $sec[2] );
		if ( stripos( $title, 'get in touch' ) !== false ) continue;

		$t = strtolower( $title );
		if ( strpos( $t, 'founder' ) !== false ) {
			$icon_key = 'person';
		} elseif ( strpos( $t, 'why' ) !== false ) {
			$icon_key = 'shield';
		} else {
			$icon_key = 'target';
		}
		$rev = ( $i % 2 === 1 ) ? ' rev' : '';
		$i++;
	?>
	<div class="about-section<?php echo $rev; ?>">
		<div class="about-icon"><svg viewBox="0 0 24 24"><?php echo $icon_paths[ $icon_key ]; ?></svg></div>
		<div class="about-body">
			<h2><?php echo esc_html( $title ); ?></h2>
			<?php echo wp_kses_post( $body ); ?>
		</div>
	</div>
	<?php endforeach; ?>
	</div>
</section>

<?php
foreach ( $sections as $sec ) :
	$title = trim( strip_tags( $sec[1] ) );
	if ( stripos( $title, 'get in touch' ) === false ) continue;
	$cta_body = trim( $sec[2] );
	?>
	<section class="about-cta">
		<div class="about-cta-inner">
			<h2><?php echo esc_html( $title ); ?></h2>
			<?php echo wp_kses_post( $cta_body ); ?>
			<p style="margin-top:24px"><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn-grad">Get in Touch</a></p>
		</div>
	</section>
	<?php
endforeach;

get_footer();
