<div class="row how-it-works">
	<section class="col-sm-10 col-sm-offset-1">
		
		<h2 class="text-center"><?php the_sub_field('col_heading') ?></h2>
		
		<?php
			
			$rows = get_sub_field('column');
			
			if( $rows ) {
				$count = count($rows);
				$colClass = '';
				
				if( $count == 3 ) {
					$colClass = 'col-sm-4';
				}
				elseif( $count == 2 ) {
					$colClass = 'col-sm-6';
				}
				else {
					$colClass = 'col-sm-12';
				}
				
				foreach( $rows as $row ) {
					echo '<div class="'. $colClass .' '. $row['text_align'] .'">';
						echo '<i class="'. $row['icon_class'] .'"></i>';
						echo '<h4>'. $row['column_heading'] .'</h4>';
						echo '<p>'. $row['column_content'] .'</p>';
					echo '</div>';
					
				}
						
				
			}
					
		?>
	
	</section>
</div>