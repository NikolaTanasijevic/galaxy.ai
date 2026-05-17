<?php
$domain_title = isset($args['domain_title']) ? $args['domain_title'] : get_the_title();
$bundle_title = isset($args['bundle_title']) ? $args['bundle_title'] : '';
$listing_url  = isset($args['listing_url']) ? $args['listing_url'] : '';
?>
<div class="inquiry-box">
  <h3>Make an Inquiry</h3>
  <p>Send us a message and we'll respond within 4 business hours.</p>

  <div class="form-success" id="formSuccess"></div>

  <form id="inquiryForm" novalidate>
    <?php if ($bundle_title) : ?>
    <input type="hidden" name="bundle" value="<?php echo esc_attr($bundle_title); ?>">
    <?php endif; ?>
    <input type="hidden" name="domain" value="<?php echo esc_attr($domain_title); ?>">
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('gm_inquiry'); ?>">

    <div class="form-field">
      <label for="buyer_name">Your Name *</label>
      <input type="text" id="buyer_name" name="buyer_name" required placeholder="Jane Smith">
    </div>
    <div class="form-field">
      <label for="buyer_email">Email Address *</label>
      <input type="email" id="buyer_email" name="buyer_email" required placeholder="jane@company.com">
    </div>
    <div class="form-field">
      <label for="buyer_message">Message</label>
      <textarea id="buyer_message" name="buyer_message" rows="4" placeholder="Tell us about your intended use or make an offer..."></textarea>
    </div>
    <button type="submit" class="btn-inquire">Send Inquiry</button>
  </form>

  <?php if ($listing_url) : ?>
  <p class="inquiry-ext">
    Also available at <a href="<?php echo esc_url($listing_url); ?>" target="_blank" rel="noopener">external marketplace</a>
  </p>
  <?php endif; ?>
</div>
