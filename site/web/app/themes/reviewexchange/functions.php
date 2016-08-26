<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php' // Theme customizer
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

function above_content() {
	do_action('above_content');
}

function below_content() {
	do_action('below_content');
}

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page();
	
}

function custom_maybe_activate_user() {

    $template_path = STYLESHEETPATH . '/activate.php';
    $is_activate_page = isset( $_GET['page'] ) && $_GET['page'] == 'gf_activation';
    
    if( ! file_exists( $template_path ) || ! $is_activate_page  )
        return;
    
    require_once( $template_path );
    
    exit();
}
add_action('wp', 'custom_maybe_activate_user', 9);




/**
 * Register new statuses - add an array for each status
 * Tutorial: http://www.sellwithwp.com/woocommerce-custom-order-status-2/
**/
function register_new_wc_order_statuses() {
    register_post_status( 'wc-matching', array(
        'label'                     => 'Matching',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Matching <span class="count">(%s)</span>', 'Matching <span class="count">(%s)</span>' )
    ) );
    register_post_status( 'wc-matched', array(
        'label'                     => 'Matched',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Matched <span class="count">(%s)</span>', 'Matched <span class="count">(%s)</span>' )
    ) );
    // repeat register_post_status() for each new status
}
add_action( 'init', 'register_new_wc_order_statuses' );
 
 
// Add new statuses to list of WC Order statuses
function add_new_wc_statuses_to_order_statuses( $order_statuses ) {
 
    $new_order_statuses = array();
 
    // add new order statuses after processing
    foreach ( $order_statuses as $key => $status ) {
 
        $new_order_statuses[ $key ] = $status;
 
        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-matching'] = 'Matching';
            $new_order_statuses['wc-matched'] = 'Matched';
            // Add a $new_order_statuses[key] = value; for each status you've added (in the order you want)
        }
    }
 
    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_new_wc_statuses_to_order_statuses' );

function wpb_woo_my_account_order() {
	$myorder = array(
		'dashboard'       => __( 'Dashboard', 'woocommerce' ),
		'orders'          => __( 'My Matches', 'woocommerce' ),
		'downloads'       => __( 'Downloads', 'woocommerce' ),
		'edit-address'    => __( 'Address', 'woocommerce' ),
		'payment-methods' => __( 'Payment Methods', 'woocommerce' ),
		'edit-account'    => __( 'Email and Password', 'woocommerce' ),
		'customer-logout' => __( 'Logout', 'woocommerce' ),
	);
	return $myorder;
}
add_filter ( 'woocommerce_account_menu_items', 'wpb_woo_my_account_order' );

/*
 * Change the entry title of the endpoints that appear in My Account Page - WooCommerce 2.6
 * Using the_title filter
 */
function wpb_woo_endpoint_title( $title, $id ) {
	if ( is_wc_endpoint_url( 'orders' ) && in_the_loop() ) {
		$title = "My Matches";
	}
	if( is_wc_endpoint_url( 'edit-account' ) && in_the_loop() ) {
		$title = "Email and Password";
	}
	if( is_wc_endpoint_url( 'edit-address' ) && in_the_loop() ) {
		$title = "Address";
	}
	
	return $title;
}
add_filter( 'the_title', 'wpb_woo_endpoint_title', 10, 2 );


/*
 * Add custom endpoint that appears in My Account Page - WooCommerce 2.6
 * Ref - https://gist.github.com/claudiosmweb/a79f4e3992ae96cb821d3b357834a005#file-custom-my-account-endpoint-php
 */


class Referral_Link {

	/**
	 * Custom endpoint name.
	 *
	 * @var string
	 */
	public static $endpoint = 'referral-link';

	/**
	 * Plugin actions.
	 */
	public function __construct() {
		// Actions used to insert a new endpoint in the WordPress.
		add_action( 'init', array( $this, 'add_endpoints' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Change the My Accout page title.
		add_filter( 'the_title', array( $this, 'endpoint_title' ) );

		// Insering your new tab/page into the My Account page.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
		add_action( 'woocommerce_account_' . self::$endpoint .  '_endpoint', array( $this, 'endpoint_content' ) );
	}

	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	public function add_endpoints() {
		add_rewrite_endpoint( self::$endpoint, EP_ROOT | EP_PAGES );
	}

	/**
	 * Add new query var.
	 *
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = self::$endpoint;

		return $vars;
	}

	/**
	 * Set endpoint title.
	 *
	 * @param string $title
	 * @return string
	 */
	public function endpoint_title( $title ) {
		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars[ self::$endpoint ] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'Referral Link', 'woocommerce' );

			remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
		}

		return $title;
	}

	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @param array $items
	 * @return array
	 */
	public function new_menu_items( $items ) {
		// Remove the logout menu item.
		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );
		// Insert your custom endpoint.
		$items[ self::$endpoint ] = __( 'Referral Link', 'woocommerce' );

		// Insert back the logout item.
		$items['customer-logout'] = $logout;

		return $items;
	}

	/**
	 * Endpoint HTML content.
	 */
	public function endpoint_content() { ?>

		<div class="woocommerce-MyAccount-content">
			
			<?php the_field('referral_is_content', 'option'); ?>

			<?php do_action( 'woocommerce_before_my_account' ); ?>

		</div>

		<?php
	}

	/**
	 * Plugin install action.
	 * Flush rewrite rules to make our custom endpoint available.
	 */
	public static function install() {
		flush_rewrite_rules();
	}
}

new Referral_Link();

// Flush rewrite rules on plugin activation.
add_action( 'after_switch_theme', array( 'Referral_Link', 'install' ) );


/*
 * Add custom endpoint that appears in My Account Page - WooCommerce 2.6
 * Ref - https://gist.github.com/claudiosmweb/a79f4e3992ae96cb821d3b357834a005#file-custom-my-account-endpoint-php
 */


class Reviewer_Preferences  {

	/**
	 * Custom endpoint name.
	 *
	 * @var string
	 */
	public static $endpoint = 'reviewer-preferences';

	/**
	 * Plugin actions.
	 */
	public function __construct() {
		// Actions used to insert a new endpoint in the WordPress.
		add_action( 'init', array( $this, 'add_endpoints' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Change the My Accout page title.
		add_filter( 'the_title', array( $this, 'endpoint_title' ) );

		// Insering your new tab/page into the My Account page.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
		add_action( 'woocommerce_account_' . self::$endpoint .  '_endpoint', array( $this, 'endpoint_content' ) );
	}

	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	public function add_endpoints() {
		add_rewrite_endpoint( self::$endpoint, EP_ROOT | EP_PAGES );
	}

	/**
	 * Add new query var.
	 *
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = self::$endpoint;

		return $vars;
	}

	/**
	 * Set endpoint title.
	 *
	 * @param string $title
	 * @return string
	 */
	public function endpoint_title( $title ) {
		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars[ self::$endpoint ] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'Reviewer Preferences', 'woocommerce' );

			remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
		}

		return $title;
	}

	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @param array $items
	 * @return array
	 */
	public function new_menu_items( $items ) {
		// Remove the logout menu item.
		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );
		// Insert your custom endpoint.
		$items[ self::$endpoint ] = __( 'Reviewer Preferences', 'woocommerce' );

		// Insert back the logout item.
		$items['customer-logout'] = $logout;

		return $items;
	}

	/**
	 * Endpoint HTML content.
	 */
	public function endpoint_content() { ?>

		<div class="woocommerce-MyAccount-content">
			
			<?php the_field('reviewer_preferences_content', 'option'); ?>

			<?php gravity_form( 2, false, false, false, '', false ); ?>

		</div>

		<?php
	}

	/**
	 * Plugin install action.
	 * Flush rewrite rules to make our custom endpoint available.
	 */
	public static function install() {
		flush_rewrite_rules();
	}
}

new Reviewer_Preferences();

// Flush rewrite rules on plugin activation.
add_action( 'after_switch_theme', array( 'Reviewer_Preferences', 'install' ) );


/*
 * Add custom endpoint that appears in My Account Page - WooCommerce 2.6
 * Ref - https://gist.github.com/claudiosmweb/a79f4e3992ae96cb821d3b357834a005#file-custom-my-account-endpoint-php
 */


class My_Books  {

	/**
	 * Custom endpoint name.
	 *
	 * @var string
	 */
	public static $endpoint = 'my-books';

	/**
	 * Plugin actions.
	 */
	public function __construct() {
		// Actions used to insert a new endpoint in the WordPress.
		add_action( 'init', array( $this, 'add_endpoints' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Change the My Accout page title.
		add_filter( 'the_title', array( $this, 'endpoint_title' ) );

		// Insering your new tab/page into the My Account page.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
		add_action( 'woocommerce_account_' . self::$endpoint .  '_endpoint', array( $this, 'endpoint_content' ) );
	}

	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	public function add_endpoints() {
		add_rewrite_endpoint( self::$endpoint, EP_ROOT | EP_PAGES );
	}

	/**
	 * Add new query var.
	 *
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = self::$endpoint;

		return $vars;
	}

	/**
	 * Set endpoint title.
	 *
	 * @param string $title
	 * @return string
	 */
	public function endpoint_title( $title ) {
		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars[ self::$endpoint ] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'My Books', 'woocommerce' );

			remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
		}

		return $title;
	}

	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @param array $items
	 * @return array
	 */
	public function new_menu_items( $items ) {
		// Remove the logout menu item.
		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );
		// Insert your custom endpoint.
		$items[ self::$endpoint ] = __( 'My Books', 'woocommerce' );

		// Insert back the logout item.
		$items['customer-logout'] = $logout;

		return $items;
	}

	/**
	 * Endpoint HTML content.
	 */
	public function endpoint_content() { ?>

		<div class="woocommerce-MyAccount-content">
			
			<h2>My Books</h2>
			
			<?php get_template_part('templates/acf/add-books'); ?>

		</div>

		<?php
	}

	/**
	 * Plugin install action.
	 * Flush rewrite rules to make our custom endpoint available.
	 */
	public static function install() {
		flush_rewrite_rules();
	}
}

new My_Books();

// Flush rewrite rules on plugin activation.
add_action( 'after_switch_theme', array( 'My_Books', 'install' ) );



function dashboard_helper() {
	
	the_field('dashboard_intro', 'option');
	
	// check if the repeater field has rows of data
	if( have_rows('helper_sections', 'option') ):
	
		// loop through the rows of data
		while ( have_rows('helper_sections', 'option') ) : the_row(); ?>
		<div class="helper">
			<h4>
				<a href="<?php the_sub_field('heading_link_helper'); ?>">
					<?php the_sub_field('heading_helper'); ?>
				</a>
			</h4>
			<?php the_sub_field('content_helper'); ?>
		</div>
		
		<?php endwhile;
	
	else :
	
		// no rows found
	
	endif;	
	
	
}
add_action( 'woocommerce_after_my_account', 'dashboard_helper', 8 );

add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );
function woo_custom_cart_button_text() {
	//return __( 'Purchase a Reviewer Match for my Book', 'woocommerce' );
	return __( 'Match Me', 'woocommerce' );
}

function completed_form() {
	
$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

	$reviewer = '';
	if (strpos($url,'my-account/reviewer-preferences') !== false) {
	  $reviewer = 'hidden';
	} else {
	  $reviewer = 'visible';
	}
	
	
	global $current_user;
	wp_get_current_user();
	$complete = esc_attr( $current_user->completed_review_prefrences );
	if( is_user_logged_in() ) {
		if( $complete !== "Yes" ) {
			echo '<div class="alert alert-danger animated bounceInDown '. $reviewer .'" role="alert">';
				echo '<div class="container">';
					echo '<div class="row text-center">';
						echo '<div class="col-sm-8 col-sm-offset-2">';
							echo '<h2>'. get_field('popup_header', 'option') .'</h2>';
							echo get_field('popup_content', 'option');
						echo '</div>';
						echo '<div class="col-sm-12">';
							echo '<a class="btn btn-primary btn-lg" href="'. get_field('popup_button_url', 'option') .'">'. get_field('popup_button_text', 'option') .'</a>';
						echo '</div>';
						echo '<div class="col-sm-12">';
							echo '<h4>For More Information See Below</h4>';
							echo '<div class="row">';
								echo '<div class="col-sm-3">';
									echo '<a href="'. get_permalink(63) .'" class="btn btn-sm btn-block btn-default">About</a>';
								echo '</div>';
								echo '<div class="col-sm-3">';
									echo '<a href="/faqs" class="btn btn-sm btn-block btn-default">FAQs</a>';
								echo '</div>';
								echo '<div class="col-sm-3">';
									echo '<a href="'. get_permalink(65) .'" class="btn btn-sm btn-block btn-default">Contact</a>';								
								echo '</div>';
								echo '<div class="col-sm-3">';
									echo '<a href="'. get_permalink(53) .'" class="btn btn-sm btn-block btn-default">Terms &amp; Conditions</a>';								
								echo '</div>';								
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		}
	}
	
}

function completed_form_activate() {
	
$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

	$reviewer = '';
	if (strpos($url,'my-account/reviewer-preferences') !== false) {
	  $reviewer = 'hidden';
	} else {
	  $reviewer = 'visible';
	}
	
	
	global $current_user;
	wp_get_current_user();
	$complete = esc_attr( $current_user->completed_review_prefrences );
	if( is_user_logged_in() ) {
		if( $complete !== "Yes" ) {
			echo '<div class="alert alert-danger animated bounceInDown '. $reviewer .'" role="alert">';
				echo '<div class="container">';
					echo '<div class="row text-center">';
						echo '<div class="col-sm-8 col-sm-offset-2">';
							echo '<h2>'. get_field('activate_header', 'option') .'</h2>';
							echo get_field('activate_content', 'option');
						echo '</div>';
						echo '<div class="col-sm-12">';
							echo '<a class="btn btn-primary btn-lg" href="'. get_field('activate_button_url', 'option') .'">'. get_field('activate_button_text', 'option') .'</a>';
						echo '</div>';
						echo '<div class="col-sm-12">';
							echo '<h4>For More Information See Below</h4>';
							echo '<div class="row">';
								echo '<div class="col-sm-3">';
									echo '<a href="'. get_permalink(63) .'" class="btn btn-sm btn-block btn-default">About</a>';
								echo '</div>';
								echo '<div class="col-sm-3">';
									echo '<a href="/faqs" class="btn btn-sm btn-block btn-default">FAQs</a>';
								echo '</div>';
								echo '<div class="col-sm-3">';
									echo '<a href="'. get_permalink(65) .'" class="btn btn-sm btn-block btn-default">Contact</a>';								
								echo '</div>';
								echo '<div class="col-sm-3">';
									echo '<a href="'. get_permalink(53) .'" class="btn btn-sm btn-block btn-default">Terms &amp; Conditions</a>';								
								echo '</div>';								
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		}
	}
	
}

add_filter( 'woocommerce_add_cart_item_data', 'woo_custom_add_to_cart' );
 
function woo_custom_add_to_cart( $cart_item_data ) {
global $woocommerce;
$woocommerce->cart->empty_cart();
 
return $cart_item_data;
}

function select_book() {

	global $current_user;
	global $post;
	$args = array(
		'post_type' => 'books',
		'author' => $current_user->ID
	);
	// The Query
	$book_query = new WP_Query( $args );
	
	
	
	// The Loop
	if ( $book_query->have_posts() ) {
		$i = 1;
		echo '<div class="product-addon my-books">';
			echo '<h3 class="addon-name">Choose a Book <abbr class="required" title="Required field">*</abbr></h3>';
			echo '<div class="addon-description">';
				echo '<p>Select a book for review</p>';
			echo '</div>';
			echo '<p class="form-row form-row-wide">';
				echo '<select id="myBooks" name="my-books">';
					echo '<option selected="true" disabled="true">Select a Book</option>';
					while ( $book_query->have_posts() ) {
						$book_query->the_post(); 
						$post_slug = $post->post_name;
						$selected = '';
						if( $i == 1 ) {
							$selected = 'selected="true"';
						}
						
						?>
						<option <?php echo $selected; ?> value="<?php echo $post_slug; ?>" data-bookid="<?php echo $post->ID; ?>" data-booktitle="<?php echo get_the_title(); ?>">
							<?php 
								the_title(); 
							?>
						</option>
					<?php $i++; } 
				echo '</select>';
			echo '</p>';
		echo '</div>';
		?>
		<div class="product-addon add-book-button">
			<button type="button" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#myModal">
		  	Upload a Book
			</button>
		</div>		
		
		<?php
		/* Restore original Post Data */
		wp_reset_postdata();
	} else { ?>
		<div class="product-addon add-book-button no-padding">
			<button type="button" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#myModal">
		  	Upload a Book
			</button>
		</div>		
	<?php } ?>

<?php }
add_action('woocommerce_before_add_to_cart_button', 'select_book', 5);

function add_book() { ?>
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

<?php 
	
}
add_action('woocommerce_after_single_product', 'add_book', 5);

?>