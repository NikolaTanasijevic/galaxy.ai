<footer>
  <div class="footer-top">
    <div class="footer-brand">
      <div class="f-logo">GALAXY<span class="dot"></span></div>
      <p>The world's most curated premium domain marketplace. Connecting buyers and sellers since 2019.</p>
    </div>
    <div class="footer-col">
      <h4>Marketplace</h4>
      <ul>
        <li><a href="<?php echo get_post_type_archive_link('domain'); ?>">Browse All Domains</a></li>
        <?php
        $cats = get_terms(['taxonomy' => 'domain_cat', 'hide_empty' => true, 'number' => 4]);
        foreach ($cats as $cat) :
        ?>
        <li><a href="<?php echo get_term_link($cat); ?>"><?php echo esc_html($cat->name); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Company</h4>
      <ul>
        <li><a href="<?php echo home_url('/about'); ?>">About Galaxa</a></li>
        <li><a href="<?php echo home_url('/#how'); ?>">How It Works</a></li>
        <li><a href="<?php echo home_url('/contact'); ?>">Contact Us</a></li>
        <li><a href="<?php echo home_url('/privacy-policy'); ?>">Privacy Policy</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Portfolios</h4>
      <ul>
        <?php
        $bundles = get_posts(['post_type' => 'domain_bundle', 'posts_per_page' => 4, 'orderby' => 'title', 'order' => 'ASC']);
        foreach ($bundles as $bundle) :
        ?>
        <li><a href="<?php echo get_permalink($bundle->ID); ?>"><?php echo esc_html($bundle->post_title); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; <?php echo date('Y'); ?> Galaxa Media. All rights reserved.</p>
    <div class="footer-social">
      <a href="#">Twitter</a>
      <a href="#">LinkedIn</a>
      <a href="#">Instagram</a>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
