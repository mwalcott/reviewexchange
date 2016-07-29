<div class="row call-to-action">
	<section class="col-xs-8 col-xs-offset-2 col-sm-4 col-sm-offset-4 text-center">
		<h3><?php the_sub_field('cta_heading'); ?></h3>
		<p>
			<?php if( is_user_logged_in() ) { ?>
				<a href="<?php echo get_permalink(44); ?>" class="btn btn-primary btn-lg">Request A Match</a>
			<?php } else { ?>
				<a href="<?php the_sub_field('cta_button_link'); ?>" class="btn btn-primary btn-lg"><?php the_sub_field('cta_button_text'); ?></a>
			<?php } ?>
		</p>
	</section>
</div>