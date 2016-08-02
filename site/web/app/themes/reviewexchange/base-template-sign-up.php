<?php

use Roots\Sage\Setup;
use Roots\Sage\Wrapper;

?>

<!doctype html>
<html <?php language_attributes(); ?>>
  <?php get_template_part('templates/head'); ?>
  <body <?php body_class(); ?>>
    <!--[if IE]>
      <div class="alert alert-warning">
        <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage'); ?>
      </div>
    <![endif]-->
    <div class="border border-top"></div>
    <?php
      do_action('get_header');
      //get_template_part('templates/header');
    ?>
    
    <div class="wrap container" role="document">
      <div class="content row">
        <main class="col-sm-8 col-sm-offset-2">
			    <a class="brand" href="<?= esc_url(home_url('/')); ?>">
				    Review Exchange
				    <span>
					    <i class="fa fa-star" aria-hidden="true"></i>
					    <i class="fa fa-star" aria-hidden="true"></i>
					    <i class="fa fa-star" aria-hidden="true"></i>
					    <i class="fa fa-star" aria-hidden="true"></i>
					    <i class="fa fa-star" aria-hidden="true"></i>
				    </span>
				  </a>

          <?php include Wrapper\template_path(); ?>
        </main><!-- /.main -->
      </div><!-- /.content -->
    </div><!-- /.wrap -->
    <?php
      do_action('get_footer');
      //get_template_part('templates/footer');
      wp_footer();
    ?>
    
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Terms &amp; Conditions</h4>
      </div>
      <div class="modal-body">

			<?php 
				$the_query = new WP_Query( 'page_id=53' );
				while ($the_query -> have_posts()) : $the_query -> the_post();
					the_content();
				endwhile;
			?>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>    
    
    
    
  </body>
</html>
