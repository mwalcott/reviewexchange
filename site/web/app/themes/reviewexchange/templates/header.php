<?php 
	$jumbotron = '';
	if( get_field('banner') ) {
		$jumbotron = 'has-banner';
	}
	else {
		$jumbotron = '';
	}
?>
<header class="banner <?php echo $jumbotron; ?>">
  <div class="container">
    <a class="brand" href="<?= esc_url(home_url('/')); ?>">
	    Review Exchange<sup><i class="fa fa-trademark" aria-hidden="true"></i></sup>
	    <span>
		    <i class="fa fa-star" aria-hidden="true"></i>
		    <i class="fa fa-star" aria-hidden="true"></i>
		    <i class="fa fa-star" aria-hidden="true"></i>
		    <i class="fa fa-star" aria-hidden="true"></i>
		    <i class="fa fa-star" aria-hidden="true"></i>
	    </span>
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
