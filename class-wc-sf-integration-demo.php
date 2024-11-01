<?php
/**
 * Plugin Name: SubscriptionFlow Integration for WooCommerce
 * Plugin URI: https://www.subscriptionflow.com
 * Description: Use this addon to attach SubscriptionFlow Plan with any WooCommerce Product. Sync your customers on both Platforms in real-time. Manage your Subscriptions and Recurring invoices in SubscriptionFlow.
 * Version: 1.0.3
 * Author: SubscriptionFlow Team
 * Author URI: https://www.subscriptionflow.com/
 * Text Domain: subscriptionflow
 * Requires at least: 5.8
 * Requires PHP: 7.2
 *
 */

defined( 'ABSPATH' ) || exit;

define('WC_SF_DIRECTORY_PATH',plugin_dir_path( __FILE__ ));
define('WC_SF_API_V','v1');



if ( ! class_exists( 'WC_SF_Integration_Demo' ) ) :
class WC_SF_Integration_Demo {

	/**
     * 
     * Construct the plugin.
     * 
     * 
	*/

	public function __construct() {


		/**
		 * 
		 * Admin script
		 * 
		*/
		add_action('admin_enqueue_scripts', array( $this,'WC_SF_register_admin_script'));

		//add_action('wp_enqueue_scripts',  array( $this,'WC_SF_register_enqueue_scripts'));

		add_action( 'plugins_loaded', array( $this, 'WC_SF_init' ) );



	}


	/**
	 * 
	 * FrontEND script detail
	 * 
	*/
	public function WC_SF_register_enqueue_scripts() {

		

			// Register the script
			wp_register_script('custom-script', plugins_url('/front-end/js/wc-sf-custom-script.js', __FILE__), array('jquery'), rand(1,1000), true);

			// Enqueue the script
			wp_enqueue_script('custom-script');

		
	}




	/**
	 * 
	 * Admin script detail
	 * 
	*/
	
	public function WC_SF_register_admin_script($hook) {

		//make sure we are on the backend
		if (!is_admin()) return false;
		global $pagenow;

		$show_assets=false;

		if(get_post_type()=='product' && ($pagenow === 'post.php' ||  $pagenow === 'post-new.php' )){
				$show_assets=true;
		}
		if(!empty($_GET['tab']) && !empty($_GET['page'])){

			if($_GET['tab']=='wc_sf_integration_setting'){
				$show_assets=true;
			}
			
		}
		if($show_assets){

			wp_enqueue_script('wc_sf_admin_js', plugins_url('admin/js/wc-sf-admin-script.js?v='.rand(1,100000),__FILE__ ),array( 'jquery' ),rand(1,100),true);
			wp_localize_script(
				'wc_sf_admin_js',
				'wc_sf_ajax_obj',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' )
				)
			);
		
			wp_enqueue_style( 'wc_sf_admin_css', plugins_url('admin/css/wc-sf-custom-style.css?v='.rand(1,100000), __FILE__), array(), '1.0.'.rand(1,1000), 'all');

		}
		
	
	}

	


	/**
	 * 
	 * Initialize the plugin.
	 * 
	*/

	public function WC_SF_init() {

		// Checks if WooCommerce is installed.
		if ( class_exists( 'WC_Integration' ) ) {


			// Include our integration class.
			include_once 'class-wc-sf-helper.php';

			/**
			 * 
			 * Admin side
			 * 
			*/
			if (is_admin() ) {

				include_once 'admin/class-wc-sf-settings-tab.php';
				include_once 'admin/class-wc-sf-hpp-metabox.php';
				include_once 'admin/class-wc-sf-variation-metabox.php';
				include_once 'admin/wc-sf-ajax-request.php';

			}

			/**
			 * 
			 * Front end side
			 * 
			*/
			
			include_once 'front-end/wc-sf-customer-route.php';

			
			include_once 'front-end/wc-sf-product-detail-actions.php';
			include_once 'front-end/wc-sf-add-checkout.php';
		} 
		else {
		
		}
	}



}

$WC_Integration_Demo = new WC_SF_Integration_Demo();
endif;
