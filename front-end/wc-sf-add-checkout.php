<?php 

/**
 * 
 * Remove Cart Checkout Button from Cart page
 * 
 * Add SF or WC/SF both Checkout Button
 * 
*/
function WC_SF_remove_proceed_to_checkout_button() {

    $wc_sf_enable_wc_sf_checkout=get_option('wc_sf_enable_wc_sf_checkout');

    if(!empty($wc_sf_enable_wc_sf_checkout)){

        if($wc_sf_enable_wc_sf_checkout=='sf_only'){

            if (is_cart()) {
                remove_action( 'woocommerce_proceed_to_checkout','woocommerce_button_proceed_to_checkout', 20);
                add_action( 'woocommerce_proceed_to_checkout', 'WC_SF_woocommerce_button_proceed_to_checkout', 20 );
           
            }
        }
        if($wc_sf_enable_wc_sf_checkout=='both'){

            if (is_cart()) {

                add_action( 'woocommerce_proceed_to_checkout', 'WC_SF_woocommerce_button_proceed_to_checkout', 20 );
           
            }
        }
            
    
    }
  
}
add_action('template_redirect', 'WC_SF_remove_proceed_to_checkout_button');


function WC_SF_woocommerce_button_proceed_to_checkout(){

    global $woocommerce;
    $items = $woocommerce->cart->get_cart();

    $wc_sf_commerceflow_link='';
    if(!empty($items )){

        $counter=0;
        foreach($items as $item => $values) { 

            if(!empty($values['variation_id'])){

                $wc_sf_handle_checkout_product_id=get_post_meta($values['variation_id'], '_wc_sf_product_variation_field',true);
                $wc_sf_handle_checkout_plan_id_name=get_post_meta($values['variation_id'], '_wc_sf_product_plan_variation_field',true);

            }
            else{

                $wc_sf_handle_checkout_product_id=get_post_meta($values['product_id'], 'wc_sf_handle_checkout_product_id',true);
                $wc_sf_handle_checkout_plan_id_name=get_post_meta($values['product_id'], 'wc_sf_handle_checkout_plan_id_name',true);
            }
          


           $quantity=$values['quantity'];
       
           if(!empty( $wc_sf_handle_checkout_plan_id_name)){

                $wc_sf_plan_id = substr($wc_sf_handle_checkout_plan_id_name,0, strpos($wc_sf_handle_checkout_plan_id_name, "|"));
                if(!empty( $wc_sf_plan_id)){

                    $wc_sf_commerceflow_link.="items[".$counter."][pr]=$wc_sf_handle_checkout_product_id&items[".$counter."][pl]=$wc_sf_plan_id&items[".$counter."][q]=$quantity&";

                    $counter++;
                }
           }

            
                
        } 

    }

   $wc_sf_settings_tab_domain=get_option('wc_sf_settings_tab_domain');
   $wc_sf_button_wc_sf_checkout=get_option('wc_sf_button_wc_sf_checkout');

   $style="display:block!important;";
   $wc_sf_checkout_button_bg=get_option('wc_sf_checkout_button_bg');
   $wc_sf_checkout_button_text_color=get_option('wc_sf_checkout_button_text_color');
   $wc_sf_checkout_target_type=get_option('wc_sf_checkout_target_type');

   if(!empty($wc_sf_checkout_button_bg)){
        $style.="background:$wc_sf_checkout_button_bg;";
   }
    if(!empty($wc_sf_checkout_button_text_color)){
        $style.="color:$wc_sf_checkout_button_text_color;";
    }


      
   if(!empty($wc_sf_settings_tab_domain) && !empty($wc_sf_commerceflow_link))
        echo wp_kses_post('<a href="'.$wc_sf_settings_tab_domain.'/en/hosted-page/commerceflow?'.$wc_sf_commerceflow_link.'cart='.home_url().'" class="wc-sf-checkout checkout-button button alt wc-forward"  '.(!empty($style) ? "style='$style'" : "").' target="'.($wc_sf_checkout_target_type=='yes'  ? '_blank' : '').'">'.esc_html((!empty($wc_sf_button_wc_sf_checkout) ? $wc_sf_button_wc_sf_checkout : 'Subscribe Now')).'</a>');
}
