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
		echo '<ul>';
		while ( $the_query->have_posts() ) {
			$the_query->the_post(); ?>
			<li>
				<?php the_title(); ?>
				<a class="pull-right" href="#FIXME">
					<i class="fa fa-pencil" aria-hidden="true"></i>
				</a>
				
<?php 
/*
	acf_form(
		array(
			'post_id'	=> $post->ID,
			'post_title'	=> true,
			'submit_value'	=> 'Update Book',
			'updated_message' => false
		)
	); 
*/
?>				
				
				
			</li>
		<?php }
		echo '</ul>';
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		// no posts found
	}		
?>

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
