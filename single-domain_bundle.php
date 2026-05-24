<?php get_header(); the_post(); ?>

<?php
$post_id    = get_the_ID();
$domain_ids = get_post_meta($post_id, 'gm_bundle_domain_ids', true) ?: [];
$content    = get_the_content();
?>

<div class="bundle-page-wrap">

  <div class="bundle-header">
    <div class="section-eyebrow">Portfolio Bundle</div>
    <h1><?php the_title(); ?></h1>
    <?php if ($content) : ?>
    <div class="bundle-intro"><?php echo wp_kses_post($content); ?></div>
    <?php endif; ?>
  </div>

  <?php if ($domain_ids) : ?>
  <div class="domains-grid" id="domainsGrid" style="margin-bottom:64px">
    <?php
    $domains = get_posts([
      'post_type'      => 'domain',
      'post__in'       => $domain_ids,
      'orderby'        => 'post__in',
      'posts_per_page' => -1,
    ]);
    foreach ($domains as $post) :
      setup_postdata($post);
      get_template_part('template-parts/domain-card');
    endforeach;
    wp_reset_postdata();
    ?>
  </div>
  <?php endif; ?>

  <div style="max-width:580px;margin:0 auto">
    <?php
    get_template_part('template-parts/inquiry-form', null, [
      'bundle_title' => get_the_title($post_id),
      'domain_title' => '',
    ]);
    ?>
    <p style="text-align:center;margin-top:16px;font-size:13px;color:var(--dim)">
      Inquire about the full bundle — we'll respond within 4 business hours.
    </p>
  </div>

</div>

<?php get_footer(); ?>
