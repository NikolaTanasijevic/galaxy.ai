<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav id="nav">
  <a href="<?php echo home_url('/'); ?>" class="nav-logo">GALAXA<span class="dot"></span></a>
  <ul class="nav-links">
    <li><a href="<?php echo get_post_type_archive_link('domain'); ?>">Browse Domains</a></li>
    <li><a href="<?php echo home_url('/#how'); ?>">How It Works</a></li>
    <li><a href="<?php echo home_url('/about'); ?>">About</a></li>
    <li><a href="<?php echo home_url('/contact'); ?>">Contact</a></li>
  </ul>
  <div class="nav-right">
    <a href="<?php echo home_url('/contact'); ?>" class="btn-nav-ghost">Get in Touch</a>
    <a href="<?php echo get_post_type_archive_link('domain'); ?>" class="btn-nav-solid">Browse Domains</a>
  </div>
  <div class="ham" id="ham"><span></span><span></span><span></span></div>
</nav>

<div class="mob-menu" id="mobMenu">
  <span class="mob-close" id="mobClose">✕</span>
  <a href="<?php echo get_post_type_archive_link('domain'); ?>" class="mob-link">Browse Domains</a>
  <a href="<?php echo home_url('/#how'); ?>" class="mob-link">How It Works</a>
  <a href="<?php echo home_url('/about'); ?>" class="mob-link">About</a>
  <a href="<?php echo home_url('/contact'); ?>" class="mob-link">Contact</a>
</div>
