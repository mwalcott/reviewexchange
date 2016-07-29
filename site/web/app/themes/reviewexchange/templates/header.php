<header class="banner">
  <div class="container">
    <a class="brand" href="<?= esc_url(home_url('/')); ?>">
	  	1<span class="rotate">for</span>1<span class="sub">Reviews</span>
	  </a>
    <nav class="nav-user pull-right">
      <?php
      if (has_nav_menu('user_navigation')) :
        wp_nav_menu(['theme_location' => 'user_navigation', 'menu_class' => 'nav']);
      endif;
      ?>
    </nav>
    <nav class="nav-primary pull-left">
      <?php
      if (has_nav_menu('primary_navigation')) :
        wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']);
      endif;
      ?>
    </nav>
  </div>
</header>
