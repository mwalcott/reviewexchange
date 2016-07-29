<footer class="content-info navbar-fixed-bottom">
  <div class="container">
    <?php dynamic_sidebar('sidebar-footer'); ?>

		<div class="row">    
		    
		    <div class="col-sm-6 col-sm-push-6 text-right footer-links">
		
					<ul class="pull-right social">
			    <?php 
				    
						// check if the repeater field has rows of data
						if( have_rows('social', 'option') ):
					
							// loop through the rows of data
							while ( have_rows('social', 'option') ) : the_row(); ?>
		
							<li>
								<a href="<?php the_sub_field('social_account_link'); ?>" rel="nofollow" target="_blank">
									<i class="fa fa-<?php the_sub_field('social_provider'); ?>" aria-hidden="true"></i>
								</a>
							</li>
		
							
							<?php endwhile;
					
						else :
						
							// no rows found
						
						endif;			    
							    
				    
				    
			    ?>
					</ul>
		
			    <?php
			    if (has_nav_menu('footer_navigation')) :
			      wp_nav_menu(['theme_location' => 'footer_navigation', 'menu_class' => 'footer-nav']);
			    endif;
			    ?>
		
		    </div>
		    
		    <div class="col-sm-6 col-sm-pull-6 copyright">
			    <div class="copyright">
				    Copyright &copy; <?php echo date('Y'); ?> BookFuel. All Rights Reserved		    
			    </div>
		    </div>
		    
		</div>
    
  </div>
</footer>
