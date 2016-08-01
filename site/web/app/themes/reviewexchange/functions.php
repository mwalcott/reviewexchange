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
 * Register new status
 * Tutorial: http://www.sellwithwp.com/woocommerce-custom-order-status-2/
**/
function register_awaiting_shipment_order_status() {
    register_post_status( 'wc-awaiting-shipment', array(
        'label'                     => 'Matched',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Matched <span class="count">(%s)</span>', 'Matched <span class="count">(%s)</span>' )
    ) ); 

}
add_action( 'init', 'register_awaiting_shipment_order_status' );

// Add to list of WC Order statuses
function add_awaiting_shipment_to_order_statuses( $order_statuses ) {

    $new_order_statuses = array();

    // add new order status after processing
    foreach ( $order_statuses as $key => $status ) {

        $new_order_statuses[ $key ] = $status;

        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-awaiting-shipment'] = 'Matched';
        }

    }

    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_awaiting_shipment_to_order_statuses' );


function wpb_woo_my_account_order() {
	$myorder = array(
		'dashboard'       => __( 'Dashboard', 'woocommerce' ),
		'orders'          => __( 'My Matches', 'woocommerce' ),
		'downloads'       => __( 'Downloads', 'woocommerce' ),
		'edit-address'    => __( 'Address', 'woocommerce' ),
		'payment-methods' => __( 'Payment Methods', 'woocommerce' ),
		'edit-account'    => __( 'User Profile', 'woocommerce' ),
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
		$title = "User Profile";
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


class Referral_ID {

	/**
	 * Custom endpoint name.
	 *
	 * @var string
	 */
	public static $endpoint = 'referral-id';

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
			$title = __( 'Referral ID', 'woocommerce' );

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
		$items[ self::$endpoint ] = __( 'Referral ID', 'woocommerce' );

		// Insert back the logout item.
		$items['customer-logout'] = $logout;

		return $items;
	}

	/**
	 * Endpoint HTML content.
	 */
	public function endpoint_content() { ?>

		<div class="woocommerce-MyAccount-content">

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

new Referral_ID();

// Flush rewrite rules on plugin activation.
add_action( 'after_switch_theme', array( 'Referral_ID', 'install' ) );


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

function dashboard_helper() {
	
	the_field('dashboard_intro', 'option');
	
	// check if the repeater field has rows of data
	if( have_rows('helper_sections', 'option') ):
	
		// loop through the rows of data
		while ( have_rows('helper_sections', 'option') ) : the_row(); ?>
		<div class="helper">
			<h4><?php the_sub_field('heading_helper'); ?></h4>
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
	return __( 'Purchase a Reviewer Match for my Book', 'woocommerce' );
}

function completed_form() {
	
	global $current_user;
	wp_get_current_user();
	$complete = esc_attr( $current_user->completed_review_prefrences );
	if( is_user_logged_in() ) {

		if( $complete !== "Yes" ) { ?>
			<div class="alert alert-danger" role="alert">
				<div class="row">
					<div class="col-sm-9">
						You are ready for the next step: setting your review preferences. Please go to your Reviewer Preferences Form to tell us more about what kind of book you enjoy reviewing
					</div>
					<div class="col-sm-3">
						<a class="btn btn-danger pull-right btn-md btn-block" href="/my-account/reviewer-preferences/">Reviewer Preferences Form</a>
					</div>
				</div>
			</div>
		<? }
	}
	
}

