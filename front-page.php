<?php get_header(); ?>

<!-- HERO -->
<section class="hero">
  <div class="gm-star-field" aria-hidden="true">
    <div class="gm-sf-layer"></div>
    <div class="gm-sf-layer"></div>
    <div class="gm-sf-layer"></div>
  </div>
  <div class="hero-glow" aria-hidden="true"></div>
  <div class="hero-glow2" aria-hidden="true"></div>
  <div class="hero-content">
    <div class="hero-terminal"><span class="terminal-prompt">&gt;</span> <span id="badgeTyped"></span><span class="badge-cursor">_</span></div>
    <h1>The Marketplace for<br><span class="gradient-text">Premium Domains.</span></h1>
    <p class="hero-sub">Discover, acquire, and own the most valuable domain names across AI, Finance, Technology, and beyond. Your brand starts here.</p>

    <div class="search-wrap">
      <div class="hero-search" role="search">
        <input type="text" id="heroSearch" placeholder="Search domains, keywords, or categories..." aria-label="Search domains" autocomplete="off">
        <button onclick="gmTriggerSearch()">Search</button>
      </div>
      <div class="search-dropdown" id="searchDropdown"></div>
    </div>

    <div class="hero-quick" aria-label="Popular categories">
      <?php
      $quick_cats = get_terms(['taxonomy' => 'domain_cat', 'hide_empty' => true, 'number' => 5]);
      foreach ($quick_cats as $qcat) :
      ?>
      <a href="<?php echo get_term_link($qcat); ?>"><?php echo esc_html($qcat->name); ?></a>
      <?php endforeach; ?>
    </div>

    <div class="hero-stats">
      <?php
      $total = wp_count_posts('domain')->publish;
      $cat_count = wp_count_terms('domain_cat', ['hide_empty' => true]);
      ?>
      <div class="hs-item fi">
        <div class="hs-val"><?php echo $total; ?>+</div>
        <div class="hs-label">Premium Domains</div>
      </div>
      <div class="hs-item fi d1">
        <div class="hs-val"><?php echo $cat_count; ?></div>
        <div class="hs-label">Categories</div>
      </div>
      <div class="hs-item fi d2">
        <div class="hs-val">24h</div>
        <div class="hs-label">Transfer Time</div>
      </div>
      <div class="hs-item fi d3">
        <div class="hs-val">&lt;4h</div>
        <div class="hs-label">Response Time</div>
      </div>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
<section class="categories" id="categories">
  <div style="max-width:1200px;margin:0 auto">
    <div class="section-eyebrow fi">Browse by category</div>
    <h2 class="section-title fi d1">Curated<br>collections.</h2>
    <div class="cat-grid">
      <?php
      $all_cats = get_terms(['taxonomy' => 'domain_cat', 'hide_empty' => false]);
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
      ];
      foreach ($all_cats as $i => $cat) :
        $delay = $i < 2 ? '' : ($i < 4 ? ' d1' : ($i < 6 ? ' d2' : ($i < 8 ? ' d3' : ' d4')));
        $icon  = $cat_icons[$cat->slug] ?? '✦';
      ?>
      <a href="<?php echo get_term_link($cat); ?>" class="cat-pill fi<?php echo $delay; ?>">
        <span class="cat-icon"><?php echo $icon; ?></span>
        <span class="cat-pill-name"><?php echo esc_html($cat->name); ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FEATURED DOMAINS -->
<section class="domains" id="domains">
  <div style="max-width:1200px;margin:0 auto">
    <div class="domains-toolbar">
      <p class="domains-count">Featured <strong>domains</strong></p>
      <a href="<?php echo get_post_type_archive_link('domain'); ?>" style="font-size:12px;color:var(--purple2);letter-spacing:.08em">View all →</a>
    </div>
    <div class="domains-grid">
      <?php
      $featured = new WP_Query([
        'post_type'      => 'domain',
        'posts_per_page' => 6,
        'meta_query'     => [
          ['key' => 'gm_featured', 'value' => '1'],
          ['key' => 'gm_status',   'value' => 'available'],
        ],
      ]);
      if ($featured->have_posts()) :
        while ($featured->have_posts()) : $featured->the_post();
          get_template_part('template-parts/domain-card');
        endwhile;
        wp_reset_postdata();
      else :
        // Fallback: show latest 6 available
        $latest = new WP_Query([
          'post_type'      => 'domain',
          'posts_per_page' => 6,
          'meta_query'     => [['key' => 'gm_status', 'value' => 'available']],
        ]);
        while ($latest->have_posts()) : $latest->the_post();
          get_template_part('template-parts/domain-card');
        endwhile;
        wp_reset_postdata();
      endif;
      ?>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how" id="how">
  <div class="how-inner">
    <div class="section-eyebrow fi" style="justify-content:center">How it works</div>
    <h2 class="fi d1">Acquiring a premium domain<br>has never been simpler.</h2>
    <div class="how-steps">
      <div class="how-step fi">
        <div class="step-n">01</div>
        <h3 class="step-title">Browse &amp; Discover</h3>
        <p class="step-desc">Filter by category, search by keyword, or explore our curated collections. Every domain is verified and available for immediate acquisition.</p>
      </div>
      <div class="how-step fi d1">
        <div class="step-n">02</div>
        <h3 class="step-title">Inquire or Buy Now</h3>
        <p class="step-desc">Submit an inquiry for negotiated sales, or purchase instantly via the listed Buy It Now price. We respond to all inquiries within 4 business hours.</p>
      </div>
      <div class="how-step fi d2">
        <div class="step-n">03</div>
        <h3 class="step-title">Secure Transfer</h3>
        <p class="step-desc">All transfers are handled by our team via verified escrow. Funds are protected, domain is pushed to your registrar — typically within 24 hours of payment.</p>
      </div>
    </div>
  </div>
</section>

<!-- TRUST -->
<div class="trust" aria-label="Trust statistics">
  <div class="trust-item fi">
    <div class="trust-icon">
      <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    </div>
    <div class="trust-val">100%</div>
    <div class="trust-label">Verified listings<br>Ownership confirmed</div>
  </div>
  <div class="trust-item fi d1">
    <div class="trust-icon">
      <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
    </div>
    <div class="trust-val">Escrow</div>
    <div class="trust-label">Protected transactions<br>Funds held safely</div>
  </div>
  <div class="trust-item fi d2">
    <div class="trust-icon">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <div class="trust-val">&lt;4h</div>
    <div class="trust-label">Response time<br>Business hours</div>
  </div>
  <div class="trust-item fi d3">
    <div class="trust-icon">
      <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div class="trust-val">500+</div>
    <div class="trust-label">Domains sold<br>Since 2019</div>
  </div>
</div>

<?php get_footer(); ?>
