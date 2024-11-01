<?php

defined( 'ABSPATH' ) || exit;

/**
 * 
 * Variation class
 * 
 * On Product edit page
 * 
 * Showing in Variation tabe
 * 
 * 
*/
class WC_SF_Variation_Metabox {


	/**
	 * 
	 * Display the meta box HTML to the user.
	 *
	 */

	public static function WC_SF_dropdown_variation_field($loop, $variation_data, $variation) {

            /**
			 * 
			 * Get SF Product from Webshop DB
			 * 
			*/

			$wc_sf_product_list=get_option('wc_sf_product_data');

			if(!empty($wc_sf_product_list)){
				$wc_sf_product_list=json_decode($wc_sf_product_list,true);
			}	
  
            $wc_sf_product_list_map=array('' => __('Please select product'));
           if(!empty($wc_sf_product_list['data'])){
            foreach($wc_sf_product_list['data']  as $wc_sf_product_arr){
                foreach($wc_sf_product_arr  as $wc_sf_single_product){          
                        
                    $wc_sf_product_list_map[__($wc_sf_single_product['id'])]=__((!empty($wc_sf_single_product['name']) ? $wc_sf_single_product['name'] : ''));
            
                }
            }

           }

           
        $_wc_sf_product_plan_variation_field=get_post_meta($variation->ID, '_wc_sf_product_plan_variation_field', true);

   

        $_wc_sf_product_plan_variation_field_option_list=array();
        
        if(!empty($_wc_sf_product_plan_variation_field)){

            if(strpos($_wc_sf_product_plan_variation_field, "|")){
                $plan_name = substr($_wc_sf_product_plan_variation_field, strpos($_wc_sf_product_plan_variation_field, "|") + 1);
               
                 $_wc_sf_product_plan_variation_field_option_list[$_wc_sf_product_plan_variation_field]=__($plan_name);
            }
           
        }
        else{
            $_wc_sf_product_plan_variation_field_option_list['']=__('Please select product first');
        }
      
        woocommerce_wp_select(array(
            'id' => '_wc_sf_product_variation_field['.$loop.']',
            'class' => 'wc-sf-product-list-dropdown',
            'label' => __('Please select SF product', 'woocommerce'),
            'options' => $wc_sf_product_list_map,
            'value' => get_post_meta($variation->ID, '_wc_sf_product_variation_field', true),
            'wrapper_class' => 'form-row form-row-first',
        ));

        
        woocommerce_wp_select(array(
            'id' => '_wc_sf_product_plan_variation_field['.$loop.']',
            'class' => 'wc-sf-product-list-dropdown',
            'label' => __('Please select SF product plan', 'woocommerce'),
            'options' => $_wc_sf_product_plan_variation_field_option_list,
            'value' => get_post_meta($variation->ID, '_wc_sf_product_plan_variation_field', true),
            'wrapper_class' => 'form-row form-row-last product-plan',
        ));

        woocommerce_wp_checkbox( 
            array( 
                'id'            => 'sf_variant_hpp_button['.$loop.']', 
                'label'         => __( ' Enable the SF HPP Button on the Product Detail Page', 'woocommerce' ), 
                'description'   => __( 'Check this box to show the SubscriptionFlow HPP button on the detail page.', 'woocommerce' ),
                'desc_tip'      => 'true',
                'value'         => get_post_meta( $variation->ID, 'sf_variant_hpp_button', true ),
                'wrapper_class' => 'form-row form-row-first',
                'cbvalue'       => 'yes'  // Value when checked
            )
        );


	}


	/**
	 * Save SF Product against Variation.
	 *
	 * @param int $variation_id, $i.
	 */
	public static function WC_SF_save_dropdown_variation_field($variation_id, $i) {
		
        $_wc_sf_product_variation_field = $_POST['_wc_sf_product_variation_field'][$i];
        $_wc_sf_product_plan_variation_field = $_POST['_wc_sf_product_plan_variation_field'][$i];



        if(!empty( $_wc_sf_product_variation_field)){

            update_post_meta($variation_id, '_wc_sf_product_variation_field', sanitize_text_field($_wc_sf_product_variation_field));
            update_post_meta($variation_id, '_wc_sf_product_plan_variation_field', sanitize_text_field($_wc_sf_product_plan_variation_field));
        }
        else{

            update_post_meta($variation_id, '_wc_sf_product_variation_field', '');
            update_post_meta($variation_id, '_wc_sf_product_plan_variation_field', '');

        }
  

        $custom_checkbox = isset( $_POST[ 'sf_variant_hpp_button'][$i] ) ? 'yes' : 'no';
        update_post_meta( $variation_id, 'sf_variant_hpp_button', $custom_checkbox );


        
	}
}

add_action('woocommerce_variation_options_pricing', array('WC_SF_Variation_Metabox', 'WC_SF_dropdown_variation_field'), 10, 3);
add_action('woocommerce_save_product_variation', array('WC_SF_Variation_Metabox', 'WC_SF_save_dropdown_variation_field'), 10, 3);



