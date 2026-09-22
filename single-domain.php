<?php get_header(); the_post(); ?>

<?php
$post_id     = get_the_ID();
$price       = gm_domain_meta($post_id, 'gm_price');
$tld         = gm_domain_meta($post_id, 'gm_tld');
$listing_url = gm_domain_meta($post_id, 'gm_listing_url');
$keywords    = gm_domain_meta($post_id, 'gm_keywords');
$short_desc  = gm_domain_meta($post_id, 'gm_short_desc');
$status      = gm_domain_meta($post_id, 'gm_status', 'available');
$featured    = gm_domain_meta($post_id, 'gm_featured');
$terms       = get_the_terms($post_id, 'domain_cat');
$cat         = $terms ? $terms[0] : null;
$domain_name = get_the_title();
$parts       = explode('.', $domain_name, 2);
$name_part   = $parts[0];
$tld_display = isset($parts[1]) ? '.' . $parts[1] : $tld;
$long_desc   = get_the_content();
?>

<div class="single-domain-wrap">

  <div class="single-domain-header">
    <div>
      <?php if ($cat) : ?>
      <div class="section-eyebrow" style="margin-bottom:16px">
        <a href="<?php echo get_term_link($cat); ?>" style="color:var(--purple2);border-bottom:1px solid rgba(124,92,252,.3)">
          <?php echo esc_html($cat->name); ?>
        </a>
      </div>
      <?php endif; ?>
      <div class="single-domain-name">
        <?php echo esc_html($name_part); ?><span class="dc-ext"><?php echo esc_html($tld_display); ?></span>
      </div>
    </div>
    <div class="single-domain-price">
      <?php echo gm_format_price($post_id); ?>
    </div>
  </div>

  <div class="single-domain-body">
    <div>
      <?php if ($short_desc) : ?>
      <p class="single-domain-desc"><?php echo esc_html($short_desc); ?></p>
      <?php endif; ?>

      <?php if ($long_desc) : ?>
      <div class="single-domain-desc" style="margin-bottom:32px">
        <?php echo wp_kses_post($long_desc); ?>
      </div>
      <?php endif; ?>

      <div class="single-meta-grid">
        <?php if ($tld_display) : ?>
        <div class="single-meta-item">
          <div class="single-meta-label">TLD</div>
          <div class="single-meta-val"><?php echo esc_html($tld_display); ?></div>
        </div>
        <?php endif; ?>
        <div class="single-meta-item">
          <div class="single-meta-label">Status</div>
          <div class="single-meta-val" style="text-transform:capitalize"><?php echo esc_html(str_replace('_', ' ', $status)); ?></div>
        </div>
        <?php if ($cat) : ?>
        <div class="single-meta-item">
          <div class="single-meta-label">Category</div>
          <div class="single-meta-val"><?php echo esc_html($cat->name); ?></div>
        </div>
        <?php endif; ?>
        <?php if ($keywords) : ?>
        <div class="single-meta-item">
          <div class="single-meta-label">Keywords</div>
          <div class="single-meta-val" style="font-size:13px;font-weight:400;color:var(--muted)"><?php echo esc_html($keywords); ?></div>
        </div>
        <?php endif; ?>
      </div>

      <?php get_template_part('template-parts/related-domains', null, ['post_id' => $post_id]); ?>
    </div>

    <div class="inquiry-sidebar">
      <?php if ($portfolio = gm_domain_portfolio($post_id)) :
        $pf_price = get_post_meta($portfolio->ID, 'gm_bundle_price', true);
      ?>
      <div class="inquiry-box">
        <div class="section-eyebrow" style="margin-bottom:14px">Portfolio Only</div>
        <h3>Available exclusively as part of the <?php echo esc_html($portfolio->post_title); ?></h3>
        <p>This domain is not sold individually. Inquire about the full portfolio to acquire it.</p>
        <?php if ($pf_price) : ?>
        <div class="bundle-price" style="margin-bottom:20px"><?php echo '$' . number_format((int) $pf_price); ?></div>
        <?php endif; ?>
        <p class="inquiry-disclaimer"><?php echo esc_html(gm_short_disclaimer()); ?> <a href="<?php echo home_url('/terms-of-use'); ?>" target="_blank" rel="noopener">Terms of Use</a></p>
        <a href="<?php echo get_permalink($portfolio->ID); ?>" class="btn-inquire" style="display:block;text-align:center">View Portfolio &amp; Inquire</a>
      </div>
      <?php else :
      get_template_part('template-parts/inquiry-form', null, [
        'domain_title' => $domain_name,
        'listing_url'  => $listing_url,
      ]);
      endif; ?>
    </div>
  </div>

</div>

<?php get_footer(); ?>
