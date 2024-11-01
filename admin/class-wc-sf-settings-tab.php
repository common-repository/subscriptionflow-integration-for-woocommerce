<?php

defined( 'ABSPATH' ) || exit;

class WC_SF_Settings_Tab {

Private static $WC_SF_settng_id='wc_sf_integration_setting';

/*
 * Bootstraps the class and hooks required actions & filters.
 *
 */
public static function init() {
	add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::WC_SF_add_settings_tab_slug', 50 );
	add_action( 'woocommerce_settings_tabs_'.self::$WC_SF_settng_id, __CLASS__ . '::WC_SF_settings_tab_html' );
	add_action( 'admin_notices', __CLASS__ . '::WC_SF_setting_validations' );
	add_action( 'woocommerce_update_options_'.self::$WC_SF_settng_id, __CLASS__ . '::WC_SF_update_settings' );
}


/*
 * Add a new settings tab to the WooCommerce settings tabs array.
 *
 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
 */
public static function WC_SF_add_settings_tab_slug( $settings_tabs ) {
	$settings_tabs[self::$WC_SF_settng_id] = __( 'SubscriptionFlow', 'subscriptionflow-integration-for-woocommerce' );
	return $settings_tabs;
}


/*
 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
 *
 * @uses woocommerce_admin_fields()
 * @uses self::get_settings()
 */
public static function WC_SF_settings_tab_html() {

	
	woocommerce_admin_fields( self::WC_SF_get_settings() );
	
}

public static function WC_SF_setting_validations() {

	
	if(!empty($_POST['save']) && !empty($_REQUEST['tab'])){

		if($_POST['save']=='Save changes' && $_REQUEST['tab']==self::$WC_SF_settng_id){	

			$wc_sf_helper_class_obj = WC_SF_Helper::WC_SF_helper_object();
			$authentication_request=$wc_sf_helper_class_obj->WC_SF_generate_token_function();

			if($authentication_request['status']){
				_e('<div class="notice notice-success is-dismissible"><p>API is correctly configured</p></div>');
			}
			else{

				_e('<div class="notice notice-error is-dismissible"><p>'.(!empty($authentication_request['data']) ? $authentication_request['data'] :  'Looks like you made a mistake with the API Key').'</p></div>');

			}
		}
	}
	
	


}


/*
 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
 *
 * @uses woocommerce_update_options()
 * @uses self::get_settings()
 * 
 */
public static function WC_SF_update_settings() {

	woocommerce_update_options( self::WC_SF_get_settings() );
}


/*
 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
 *
 * @return array Array of settings for @see woocommerce_admin_fields() function.
 * 
 */
public static function WC_SF_get_settings() {


	$settings = array(
		'section_title' => array(
			'name'     => __( 'SubscriptionFlow Setting' ),
			'type'     => 'title',
			'desc'     => 'Please fill all the fields. You can find your <strong>Client ID </strong> and  <strong>Secret ID </strong> from your SubscriptionFlow account. If you want to sync Customers from SubscriptionFlow to WooCommerce. </br>Please add Webhook into SubscritionFlow account  <strong>http://example.com/wp-json/api/sf-create-update-user?customer_id={SF_ID} </strong>',
			'id'       => 'wc_sf_settings_tab_title'
		),
	
		'wc_sf_settings_tab_domain' => array(
			'name'             => __( 'Domain URL' ),
			'type'              => 'text',
			'desc'       => __( 'For example https://demo.subscriptionflow.com ' ),
			'desc_tip'          => false,
			'default'           => '',
			'placeholder' => 'https://demo.subscriptionflow.com',
			'id' => 'wc_sf_settings_tab_domain'
		),
		
		

		'wc_sf_settings_tab_client_id' => array(
			'title'             => __( 'Client ID' ),
			'type'              => 'text',
			'desc'       => __( 'Enter your Client ID Key. You can find this in SubscriptionFlow menu on top right side </br>  Administration 	Settings >  OAuth Clients.' ),
			'desc_tip'          => false,
			'default'           => '',
			'id' => 'wc_sf_settings_tab_client_id'
		),
		
		'wc_sf_settings_tab_client_secret' => array(
			'title'             => __( 'Client Secret' ),
			'type'              => 'text',
			'desc'       => __( 'Enter with your Client Secret Key. You can find this in SubscriptionFlow menu on top right side </br> Administration  Settings >  OAuth Clients.<div class="sync-sf-data"><a class="button-primary wc-sf-button" href="#" data-action="wc_sf_sync_product">Get SubscriptionFlow Products</a></div>' ),
			'desc_tip'          => false,
			'default'           => '',
			'id' => 'wc_sf_settings_tab_client_secret'
		),
		
		// 'wc_sf_settings_tab_portal_enable' => array(
		// 	'title'             => __( 'Sync Customer' ),
		// 	'type'              => 'checkbox',
		// 	'desc'       => __( 'Sync customer SubscriptionFlow/WC on creation.' ),
		// 	'desc_tip'          => false,
		// 	'default'           => 'no',
		// 	'id' => 'wc_sf_settings_tab_portal_enable'
		// ),
		'wc_sf_hpp_button_bg' => array(
			'title'             => __( 'Button Background  Color'),
			'type'              => 'color',
			'placeholder' => 'Enter color code like #229199',
			'desc'       => __( 'Use this background color code on single product page'),
			'desc_tip'          => true,
			'id' => 'wc_sf_hpp_button_bg'
		),
		'wc_sf_hpp_button_text_color' => array(
			'title'             => __( 'Button Text  Color' ),
			'type'              => 'color',
			'placeholder' => 'Enter color code like #fff',
			'desc'       => __( 'Use this background color code on single product page'),
			'desc_tip'          => true,
			'id' => 'wc_sf_hpp_button_text_color'
		),
		'wc_sf_variant_hpp_button_text' => array(
			'title'             => __( 'Button Text' ),
			'type'              => 'text',
			'placeholder' => 'Subscribe Now',
			'desc'       => __( 'This text will change the button text on the variant product detail page'),
			'desc_tip'          => true,
			'id' => 'wc_sf_variant_hpp_button_text'
		),
		'wc_sf_settings_add_custom_sf' => array(
			'title'             => __( 'Customer Option' ),
			'type'              => 'checkbox',
			'desc'       => __( 'Do you want to sync Customers from WooCommerce to SubscriptionFlow. ' ),
			'desc_tip'          => false,
			'default'           => false,
			'id' => 'wc_sf_settings_add_custom_sf'
		),
		'wc_sf_settings_add_customer_info_url' => array(
			'title'             => __( 'Customer Info HPP Link' ),
			'type'              => 'checkbox',
			'desc'       => __( 'Do you want to add customer info to the HPP link?' ),
			'desc_tip'          => false,
			'default'           => false,
			'id' => 'wc_sf_settings_add_customer_info_url'
		),
		'wc_sf_enable_wc_sf_checkout' => array(
			'title'             => __( 'Enable SubscriptionFlow Checkout Button' ),
			'type'              => 'select',
			'desc'       => __( "Please select option to add SubscriptionFlow Checkout Button" ),
			'desc_tip'          => false,
			'default'           => false,
			'options' => array(

				'' => __( 'Please Select Checkout Option' ),
				'both' => __( 'WC and SF both Checkout Button' ),
				'sf_only' => __('SF Checkout Button only')

			),
			'id' => 'wc_sf_enable_wc_sf_checkout'
		),
		'wc_sf_button_wc_sf_checkout' => array(
			'title'             => __( 'SF Checkout Button' ),
			'type'              => 'text',
			'desc'       => __( 'SubscriptionFlow Checkout Button Text' ),
			'desc_tip'          => false,
			'default'           => 'Subscribe Now',
			'id' => 'wc_sf_button_wc_sf_checkout'
		),
		'wc_sf_checkout_button_bg' => array(
			'title'             => __( 'CheckOut Button Background  Color'),
			'type'              => 'color',
			'placeholder' => 'Enter color code like #229199',
			'desc'       => __( 'Use this background color code on cart page'),
			'desc_tip'          => true,
			'id' => 'wc_sf_checkout_button_bg'
		),
		'wc_sf_checkout_button_text_color' => array(
			'title'             => __( 'CheckOut Button Text  Color' ),
			'type'              => 'color',
			'placeholder' => 'Enter color code like #fff',
			'desc'       => __( 'Use this background color code on cart page'),
			'desc_tip'          => true,
			'id' => 'wc_sf_checkout_button_text_color'
		),
		'wc_sf_checkout_target_type' => array(
			'title'             => __( 'Open Checkout Link' ),
			'type'              => 'checkbox',
			'desc'       => __( 'Do you want to open link into new window. ' ),
			'desc_tip'          => false,
			'default'           => false,
			'id' => 'wc_sf_checkout_target_type'
		),
		'section_end' => array(
			 'type' => 'sectionend',
			 'id' => 'wc_sf_settings_tab_section_end'
		)
	);

	return apply_filters( 'wc_sf_settings_tab', $settings );
}

}

WC_SF_Settings_Tab::init();