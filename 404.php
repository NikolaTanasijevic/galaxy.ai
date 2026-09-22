<?php get_header(); ?>

<section class="cat-page-hero" style="min-height:70vh;display:flex;align-items:center">
  <div style="max-width:1200px;margin:0 auto;width:100%;position:relative;z-index:1">
    <div class="section-eyebrow">Error 404</div>
    <h1 style="font-family:var(--font-h);font-weight:700;font-size:clamp(36px,5vw,60px);letter-spacing:-.02em">
      Lost in <span style="background:var(--grad);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent">space.</span>
    </h1>
    <p class="cat-page-intro">The page you're looking for doesn't exist or may have moved. The domain you want might still be here.</p>
    <p style="margin-top:32px;display:flex;gap:12px;flex-wrap:wrap">
      <a href="<?php echo get_post_type_archive_link('domain'); ?>" class="btn-grad">Browse Domains</a>
      <a href="<?php echo home_url('/'); ?>" class="btn-nav-ghost" style="padding:15px 32px;font-size:13px">Back to Home</a>
    </p>
  </div>
</section>

<?php get_footer(); ?>
