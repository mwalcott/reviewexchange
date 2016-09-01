<?php 
	$loggedCtaHeading = '';
	$loggedCtaButtonText = '';
	$loggedCtaButtonLink = '';
	
	if( is_user_logged_in() ) {
		$loggedCtaHeading = get_sub_field('cta_heading_loggedin');
		$loggedCtaButtonText = get_sub_field('cta_button_text_loggedin');
		$loggedCtaButtonLink = get_sub_field('cta_button_link_loggedin');
	} else {
		$loggedCtaHeading = get_sub_field('cta_heading');
		$loggedCtaButtonText = get_sub_field('cta_button_text');
		$loggedCtaButtonLink = get_sub_field('cta_button_link');
	}
	
?>

<div class="row call-to-action section">
	<section class="col-xs-8 col-xs-offset-2 col-sm-4 col-sm-offset-4 text-center">
		<h3><?php echo $loggedCtaHeading; ?></h3>
		<p>
			<a href="<?php echo $loggedCtaButtonLink; ?>" class="btn btn-primary btn-lg"><?php echo $loggedCtaButtonText; ?></a>
		</p>
	</section>
</div>