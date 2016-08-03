<?php 
/*
	global $current_user;
	wp_get_current_user();
	$complete = esc_attr( $current_user->completed_review_prefrences );
	if( is_user_logged_in() ) {
		if( $complete !== "Yes" ) {

		} else {
			woocommerce_content();
		}
	}
*/
?>

<?php woocommerce_content(); ?>