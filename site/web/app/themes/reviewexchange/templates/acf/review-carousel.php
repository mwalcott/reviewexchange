<div class="row review-carousel section text-center">
	<h2 class="text-center"><?php the_sub_field('rc_heading') ?></h2>
	<div id="reviews-carousel" class="carousel slide" data-ride="carousel">
		<div class="carousel-inner" role="listbox">
	
			<?php
			$i = 0; 
			$active = '';
			$args = array(
				'post_type' => 'reviews',
				'posts_per_page' => 10
			);
			
			// the query
			$the_query = new WP_Query( $args ); ?>
			
			<?php if ( $the_query->have_posts() ) : ?>
			
				<?php while ( $the_query->have_posts() ) : $the_query->the_post(); $i++; ?>
					<?php 
						if($i == 1) { 
							$active = 'active'; 
						} else {
							$active = '';
						}  
					?>
					<div class="item <?php echo $active; ?>">
						<div class="row">
							<div class="col-sm-10 col-sm-offset-1">
								<?php the_content(); ?>
								<h6>- <?php the_title(); ?> -</h6>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			
				<?php wp_reset_postdata(); ?>
			
			<?php else : ?>
				<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
			<?php endif; ?>
	
		</div>
	</div>
	<a class="left carousel-control" href="#reviews-carousel" role="button" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
	</a>
	<a class="right carousel-control" href="#reviews-carousel" role="button" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
	</a>
</div>