<?php
$post_id = isset($args['post_id']) ? $args['post_id'] : get_the_ID();
$terms   = get_the_terms($post_id, 'domain_cat');
if (!$terms) return;

$related = get_posts([
	'post_type'      => 'domain',
	'posts_per_page' => 3,
	'post__not_in'   => [$post_id],
	'tax_query'      => [[
		'taxonomy' => 'domain_cat',
		'field'    => 'term_id',
		'terms'    => wp_list_pluck($terms, 'term_id'),
	]],
	'meta_query' => [[
		'key'     => 'gm_status',
		'value'   => 'available',
		'compare' => '=',
	]],
]);

if (!$related) return;
?>
<section style="margin-top:64px">
  <div class="section-eyebrow">Related domains</div>
  <div class="domains-grid" style="margin-top:24px">
    <?php foreach ($related as $post) : setup_postdata($post); ?>
      <?php get_template_part('template-parts/domain-card'); ?>
    <?php endforeach; wp_reset_postdata(); ?>
  </div>
</section>
