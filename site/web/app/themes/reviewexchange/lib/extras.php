<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Setup;

/**
 * Add <body> classes
 */
function body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }

  // Add class if sidebar is active
  if (Setup\display_sidebar()) {
    $classes[] = 'sidebar-primary';
  }

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');


function add_loginout_link( $items, $args ) {
	if (is_user_logged_in() && $args->theme_location == 'user_navigation') {
		$items .= '<li class="menu-item menu-logout"><a class="btn btn-primary" href="/product/request-a-match">Request A Match</a></li>';
		$items .= '<li class="menu-item menu-my-account"><a href="/my-account">My Account</a></li>';
		$items .= '<li class="menu-item menu-logout"><a href="/my-account/customer-logout">Logout</a></li>';
	}
	elseif (!is_user_logged_in() && $args->theme_location == 'user_navigation') {
		$items .= '<li class="menu-item menu-signup"><a class="btn btn-primary" href="/sign-up">Sign Up FREE!</a></li>';
		$items .= '<li class="menu-item menu-login"><a href="/my-account">Log In</a></li>';
	}
	return $items;
}
add_filter( 'wp_nav_menu_items', __NAMESPACE__ . '\\add_loginout_link', 10, 2 );


// Content Builder ACF
function content_acf() { 

	// check if the flexible content field has rows of data
	if( have_rows('sections') ):
	
		// loop through the rows of data
		while ( have_rows('sections') ) : the_row();
		
			if( get_row_layout() == 'columns' )
			
				get_template_part('templates/acf/columns');

			if( get_row_layout() == 'call_to_action' )
			
				get_template_part('templates/acf/call-to-action');
									
		endwhile;
	
	else :
	
		// no layouts found
	
endif;

}
add_action('below_content', __NAMESPACE__ . '\\content_acf');

// Main Banner
function main_banner() { ?>
<?php if( get_field('heading') && get_field('content') ) { ?>
	<div class="jumbotron" style="background-image: url(<?php the_field('image'); ?>);">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-sm-offset-3 text-center banner-content-wrap">
					<div>
						<h1><?php the_field('heading'); ?></h1>						
						<?php the_field('content'); ?>
						<p><a href="<?php the_field('button_url'); ?>" class="btn btn-primary btn-lg btn-block"><?php the_field('button_text'); ?></a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<?php }
add_action('above_content', __NAMESPACE__ . '\\main_banner');


add_filter( 'get_the_archive_title', function ( $title ) {
    if( is_post_type_archive() ) {
        $title = sprintf( __( '%s' ), post_type_archive_title( '', false ) );
    }
    return $title;
});