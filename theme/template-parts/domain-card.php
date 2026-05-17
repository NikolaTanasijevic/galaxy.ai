<?php
$post_id   = get_the_ID();
$price     = gm_domain_meta($post_id, 'gm_price');
$tld       = gm_domain_meta($post_id, 'gm_tld');
$short     = gm_domain_meta($post_id, 'gm_short_desc');
$status    = gm_domain_meta($post_id, 'gm_status', 'available');
$terms     = get_the_terms($post_id, 'domain_cat');
$cat       = $terms ? $terms[0] : null;
$cat_slug  = $cat ? $cat->slug : '';
$cat_name  = $cat ? $cat->name : '';
$cat_class = gm_cat_class($cat_slug);

$domain_name = get_the_title();
$parts       = explode('.', $domain_name, 2);
$name_part   = $parts[0];
$tld_part    = isset($parts[1]) ? '.' . $parts[1] : $tld;

$is_sold = $status === 'sold';
?>
<article class="domain-card fi<?php echo $is_sold ? ' sold' : ''; ?>"
  data-cat="<?php echo esc_attr($cat_slug); ?>"
  data-price="<?php echo esc_attr($price ?: '0'); ?>"
  onclick="window.location='<?php echo get_permalink($post_id); ?>'">

  <div class="dc-header">
    <?php if ($cat_name) : ?>
    <span class="dc-cat <?php echo esc_attr($cat_class); ?>"><?php echo esc_html($cat_name); ?></span>
    <?php endif; ?>
    <?php echo gm_format_price($post_id); ?>
  </div>

  <div class="dc-domain">
    <?php echo esc_html($name_part); ?><span class="dc-ext"><?php echo esc_html($tld_part); ?></span>
  </div>

  <p class="dc-desc"><?php echo esc_html($short); ?></p>

  <div class="dc-footer">
    <div class="dc-meta">
      <?php if ($tld_part) : ?>
      <span><?php echo esc_html(ltrim($tld_part, '.')); ?> TLD</span>
      <?php endif; ?>
      <?php if ($is_sold) : ?>
      <span class="dc-dot"></span><span>Sold</span>
      <?php endif; ?>
    </div>
    <?php if (!$is_sold) : ?>
    <span class="dc-inquire">Inquire</span>
    <?php endif; ?>
  </div>
</article>
