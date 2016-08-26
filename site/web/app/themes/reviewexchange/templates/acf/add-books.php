<?php 
	global $current_user;
	$args = array(
		'post_type' => 'books',
		'author' => $current_user->ID
	);
	// The Query
	$the_query = new WP_Query( $args );
	
	// The Loop
	if ( $the_query->have_posts() ) {
		echo '<ul class="manage-books clearfix">';
		while ( $the_query->have_posts() ) {
			$the_query->the_post(); ?>
			<li>
				<?php the_title(); ?>
				<div class="actions pull-right">
					<ul class="clearfix">
						<li>
							<a class="edit-book" role="button" data-toggle="collapse" href="#collapse<?php echo $post->ID; ?>" aria-expanded="false" aria-controls="collapse<?php echo $post->ID; ?>">
								<i class="fa fa-pencil" aria-hidden="true"></i> Edit
							</a>
						</li>
					</ul>
				</div>

				<div class="collapse" id="collapse<?php echo $post->ID; ?>">
				  <div class="well">
						<?php 
							acf_form(
								array(
									'post_id'	=> $post->ID,
									'post_title'	=> true,
									'submit_value'	=> 'Update Book',
									'updated_message' => false
								)
							); 
						?>				
				  </div>
				</div>				
				
				
			</li>
		<?php }
		echo '</ul>';
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		// no posts found
	}		
?>
	<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
  	Upload a Book
	</button>

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Add a Book</h4>
	      </div>
	      <div class="modal-body">
				<?php
					$args = array(
						'post_id' => 'new_post',
						'post_title' => true,
						'new_post' => array(
							'post_type' => 'books',
							'post_status' => 'publish',
						),
						'submit_value'	=> 'Add Book',
						'updated_message' => false
					);
					acf_form( $args ); 
				?>
	      </div>
	    </div>
	  </div>
	</div>
