<?php
$b          = $args['bundle'];
$excerpt    = wp_trim_words(wp_strip_all_tags($b->post_content), 26);
$domain_ids = get_post_meta($b->ID, 'gm_bundle_domain_ids', true) ?: [];
$price      = get_post_meta($b->ID, 'gm_bundle_price', true);
?>
<a href="<?php echo get_permalink($b->ID); ?>" class="bundle-card fi">
  <div class="dc-header" style="margin-bottom:14px">
    <span class="section-eyebrow" style="margin-bottom:0">Portfolio Bundle</span>
    <span class="dc-price"><?php echo $price ? '$' . number_format((int) $price) : 'Make Offer'; ?></span>
  </div>
  <h3 class="bundle-card-title"><?php echo esc_html($b->post_title); ?></h3>
  <p class="bundle-card-desc"><?php echo esc_html($excerpt); ?></p>
  <div class="bundle-card-footer">
    <span><?php echo count($domain_ids); ?> domains</span>
    <span class="dc-inquire">View Portfolio</span>
  </div>
</a>
