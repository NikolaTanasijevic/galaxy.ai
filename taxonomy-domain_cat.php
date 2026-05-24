<?php
get_header();
$term      = get_queried_object();
$cat_icons = [
  'ai'             => '⚡',
  'finance'        => '◈',
  'travel'         => '◎',
  'technology'     => '⬡',
  'healthcare'     => '◉',
  'beauty'         => '✿',
  'startup-brands' => '▲',
  'lifestyle'      => '◐',
  'contractors'    => '⬢',
];
$icon = $cat_icons[$term->slug] ?? '✦';
$intro = get_term_meta($term->term_id, 'gm_cat_intro', true);
?>

<section class="cat-page-hero">
  <div style="max-width:1200px;margin:0 auto;position:relative;z-index:1">
    <div class="section-eyebrow"><?php echo $icon; ?> <?php echo esc_html($term->name); ?></div>
    <h1 style="font-family:var(--font-h);font-weight:700;font-size:clamp(36px,5vw,60px);letter-spacing:-.02em">
      Premium <span style="background:var(--grad);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent"><?php echo esc_html($term->name); ?></span><br>Domain Names
    </h1>
    <?php if ($intro) : ?>
    <p class="cat-page-intro"><?php echo wp_kses_post($intro); ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="domains" style="padding-top:48px">
  <div style="max-width:1200px;margin:0 auto">
    <div class="domains-toolbar">
      <p class="domains-count">
        <strong id="domainCount"><?php echo $term->count; ?></strong> <?php echo esc_html($term->name); ?> domains
      </p>
      <div class="domains-sort">
        <span>Sort by:</span>
        <select onchange="gmSortDomains(this.value)">
          <option value="featured">Featured</option>
          <option value="price-high">Price: High to Low</option>
          <option value="price-low">Price: Low to High</option>
          <option value="alpha">A–Z</option>
        </select>
      </div>
    </div>

    <div class="domains-grid" id="domainsGrid">
      <?php
      while (have_posts()) : the_post();
        get_template_part('template-parts/domain-card');
      endwhile;
      ?>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.domain-card').forEach((c, i) => c.dataset.origIndex = i);
});
</script>

<?php get_footer(); ?>
