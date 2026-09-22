<?php get_header(); ?>

<section class="cat-page-hero">
  <div style="max-width:1200px;margin:0 auto;position:relative;z-index:1">
    <div class="section-eyebrow">Sold as one</div>
    <h1 style="font-family:var(--font-h);font-weight:700;font-size:clamp(36px,5vw,60px);letter-spacing:-.02em">
      Domain <span style="background:var(--grad);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent">Portfolios</span>
    </h1>
    <p class="cat-page-intro">Curated collections of related premium domains, offered together as a single acquisition. Each portfolio has one combined price and one inquiry.</p>
  </div>
</section>

<section class="domains">
  <div style="max-width:1200px;margin:0 auto">
    <?php
    $bundles = get_posts(['post_type' => 'domain_bundle', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    if ($bundles) : ?>
    <div class="bundles-grid">
      <?php foreach ($bundles as $b) get_template_part('template-parts/bundle-card', null, ['bundle' => $b]); ?>
    </div>
    <?php else : ?>
    <p class="domains-count">No portfolios are available right now.</p>
    <?php endif; ?>
  </div>
</section>

<?php get_footer(); ?>
