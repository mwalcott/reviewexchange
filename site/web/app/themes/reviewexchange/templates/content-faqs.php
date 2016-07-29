<?php $i = 0; ?>
<?php while (have_posts()) : the_post(); $i++ ?>
	
	<?php 
		$active = '';
		if( $i == 1 ) {
			$active = 'in';	
		}
	?>

<!--
	<article <?php post_class(); ?>>
	  <header>
		  <?php echo $i; ?>
	    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
	    <?php //get_template_part('templates/entry-meta'); ?>
	  </header>
	  <div class="entry-summary">
	    <?php the_excerpt(); ?>
	  </div>
	</article>
-->
	
	
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="heading-<?php echo $i; ?>">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?php echo $i; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $i; ?>">
					<?php echo get_the_title(); ?>
        </a>
      </h4>
    </div>
    <div id="collapse-<?php echo $i; ?>" class="panel-collapse collapse <?php echo $active; ?>" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
				<?php the_content(); ?>
      </div>
    </div>
  </div>
	
	

<?php endwhile; ?>

<?php $i++; ?>