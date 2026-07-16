<?php get_header(); ?>

<section class="cat-page-hero">
  <div style="max-width:1200px;margin:0 auto;position:relative;z-index:1">
    <div class="section-eyebrow">Domain Marketplace</div>
    <h1 style="font-family:var(--font-h);font-weight:700;font-size:clamp(36px,5vw,60px);letter-spacing:-.02em">
      Browse All <span style="background:var(--grad);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent">Premium Domains</span>
    </h1>
    <p class="cat-page-intro">Search and filter across our full inventory of premium domain names.</p>

    <div class="search-wrap" style="max-width:520px;margin:32px 0 0">
      <div class="hero-search" role="search">
        <input type="text" id="archiveSearch" placeholder="Search domains or keywords..." aria-label="Search" autocomplete="off">
        <button onclick="gmArchiveSearch()">Search</button>
      </div>
      <div class="search-dropdown" id="archiveDropdown"></div>
    </div>
  </div>
</section>

<section class="categories" style="padding:40px 5%">
  <div style="max-width:1200px;margin:0 auto">
    <div class="cat-grid">
      <div class="cat-pill active" data-cat="all" onclick="gmFilterCat('all')">
        <span class="cat-icon">✦</span>
        <span class="cat-pill-name">All</span>
      </div>
      <?php
      $cats = get_terms(['taxonomy' => 'domain_cat', 'hide_empty' => true]);
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
        'audio-music-tech' => '♫',
        'automotive-mobility' => '⚙',
      ];
      foreach ($cats as $cat) :
        $icon = $cat_icons[$cat->slug] ?? '✦';
      ?>
      <div class="cat-pill" data-cat="<?php echo esc_attr($cat->slug); ?>" onclick="gmFilterCat('<?php echo esc_js($cat->slug); ?>')">
        <span class="cat-icon"><?php echo $icon; ?></span>
        <span class="cat-pill-name"><?php echo esc_html($cat->name); ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="domains">
  <div style="max-width:1200px;margin:0 auto">
    <div class="domains-toolbar">
      <p class="domains-count">Showing <strong id="domainCount">0</strong> domains</p>
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
      $paged = get_query_var('paged') ?: 1;
      $args  = [
        'post_type'      => 'domain',
        'posts_per_page' => -1,
        'orderby'        => ['meta_value_num' => 'DESC', 'title' => 'ASC'],
        'meta_key'       => 'gm_featured',
      ];
      $query = new WP_Query($args);
      while ($query->have_posts()) : $query->the_post();
        get_template_part('template-parts/domain-card');
      endwhile;
      wp_reset_postdata();
      ?>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  gmUpdateCount();
  document.querySelectorAll('.domain-card').forEach((c, i) => c.dataset.origIndex = i);
});
</script>

<?php get_footer(); ?>
