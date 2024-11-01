<?php


/**
 * 
 * Add custom meta field to product variations
 * Update json in data-product_variations 
 * 
*/

add_filter('woocommerce_available_variation', 'WC_SF_add_custom_meta_to_variation', 10, 3);
function WC_SF_add_custom_meta_to_variation($data, $product, $variation) {

    $data['_wc_sf_product_variation_field'] = get_post_meta($variation->get_id(), '_wc_sf_product_variation_field', true);
    $data['_wc_sf_product_plan_variation_field'] = get_post_meta($variation->get_id(),'_wc_sf_product_plan_variation_field', true);
    $data['sf_variant_hpp_button'] = get_post_meta($variation->get_id(),'sf_variant_hpp_button', true);

    return $data;

}


/**
 * 
 * Add SubscriptionFlow button on Product detail page
 * Hide Add to Cart button
 * 
*/

add_action( 'wp_footer',  'WC_SF_add_custom_footer_styless');

function WC_SF_add_custom_footer_styless(){

    if ( is_single() && 'product' == get_post_type() ){
    ?>
    <style>
        .single-product.wc-sf-remove-cart form.cart {
            display: none!important;
        }
        .wc-sf-hpp-links-section .wc-sf-buttons-link {
            padding: 10px 20px 10px;
        }
        .wc-sf-buttons {
            display: flex;
            flex-wrap: wrap;
        }
        .wc-sf-buttons-link:not(:first-child) {
            margin-left: 15px;
        }
        .wc-sf-buttons {
            display: flex;
            flex-wrap: wrap;
            padding: 10px 0px 10px;
        }
        .wc-sf-variant-hpp-links-section{
            display:none;
        }
    </style>

    <?php
    }
    ?>
    <style>
        header a.button.checkout {
            display: none!important;
        }
    </style>
    <script>

        jQuery(document).ready(function($) {

            if ($('.wc-sf-varintion-buttons-link').length) {

                // Function to handle variation change
                $('.variations_form').on('change', '.variation_id', function() {

                

                    // Get selected variation ID
                    var wc_sf_variationID = $(this).val();

                    if(wc_sf_variationID){

                       

                        // Get product variations data
                        var wc_sf_productVariations = $('.variations_form').data('product_variations');


                        // Find the selected variation in productVariations array
                        var wc_sf_selectedVariation = wc_sf_productVariations.find(function(variation) {
                            return variation.variation_id == wc_sf_variationID;
                        });

                        if (wc_sf_selectedVariation) {

                         

                            if(wc_sf_selectedVariation.sf_variant_hpp_button){

                                if(wc_sf_selectedVariation.sf_variant_hpp_button=='yes'){

                                    var wc_sf_variant_btn=$('.wc-sf-varintion-buttons-link');
                                    var wc_sf_domain_link=wc_sf_variant_btn.attr('data-domain');
                                    var wc_sf_url_param=wc_sf_variant_btn.attr('data-urlparam');

                                    if(wc_sf_selectedVariation._wc_sf_product_plan_variation_field.split('|') && wc_sf_selectedVariation._wc_sf_product_variation_field){

                                    
                                        $('.wc-sf-variant-hpp-links-section').show();

                                        var wc_sf_product_plan_variation_field = wc_sf_selectedVariation._wc_sf_product_plan_variation_field.split('|')[0];

                                        wc_sf_variant_btn.attr('href',wc_sf_domain_link+'/en/hosted-page/subscribe/'+wc_sf_product_plan_variation_field+'/product/'+wc_sf_selectedVariation._wc_sf_product_variation_field+(wc_sf_url_param ? wc_sf_url_param : ''));
                                        
                                    }

                                }
                                else{

                                    $('.wc-sf-variant-hpp-links-section').hide();
                                }
                               


                            }
               
                           



                        }

                    }
                    else{

                        $('.wc-sf-variant-hpp-links-section').hide();
                    }
                
                });
            }
        });



    </script>
<?php

}

/**
 * 
 * Add SF class
 * 
 * Hide data button
 * 
*/

add_filter( 'body_class', 'WC_SF_custom_body_class' );

function WC_SF_custom_body_class( $classes ) {


    if ( is_single() && 'product' == get_post_type() ){

        /**
         * 
         * Hide cart button 
         * 
         * if checkbox is checked in edit product
         *
         * */ 

        $productID = get_the_ID();
        $wc_sf_hide_cart_button =  get_post_meta($productID,'wc_sf_hide_cart_button',true);

        if(!empty($wc_sf_hide_cart_button )){

            if(  $wc_sf_hide_cart_button=='yes')
                $classes[] = 'wc-sf-remove-cart';
        }
           

    }


    return $classes;
}

/**
 * 
 * Show SF HPP button on product detail page
 * 
 * Code for variantion products
 * 
*/
add_action('woocommerce_after_add_to_cart_form','WC_SF_variantion_hpp_links_button');
function WC_SF_variantion_hpp_links_button() {

    global $product;

    if(is_single() && 'product' == get_post_type() && $product->is_type('variable') ){

        $productID = get_the_ID();
        $wc_sf_meta_repeatable_fields =  get_post_meta($productID,'wc_sf_meta_repeatable_fields',true);

        $wc_sf_hpp_button_text_color=get_option('wc_sf_hpp_button_text_color');
        $wc_sf_hpp_button_bg=get_option('wc_sf_hpp_button_bg');
        $wc_sf_settings_tab_domain=get_option('wc_sf_settings_tab_domain');
        $wc_sf_variant_hpp_button_text=get_option('wc_sf_variant_hpp_button_text');

        


        if(!empty($wc_sf_settings_tab_domain)){

            // esc_url
            $wc_sf_hpp_link=$wc_sf_settings_tab_domain;

            
            //Button style
            $wc_sf_hpp_button_color='#fff';
            if(!empty($wc_sf_hpp_button_text_color))
                $wc_sf_hpp_button_color=$wc_sf_hpp_button_text_color;

            $wc_sf_hpp_button_bg_color='#fd7b03';
            if(!empty($wc_sf_hpp_button_bg))
                $wc_sf_hpp_button_bg_color=$wc_sf_hpp_button_bg;

            
            $wc_sf_user_url_info='';

            $wc_sf_settings_add_customer_info_url=get_option('wc_sf_settings_add_customer_info_url');

            if(!empty(  $wc_sf_settings_add_customer_info_url)){
                if ( is_user_logged_in() ) {
                
                    $user_id = get_current_user_id();
                    $ai_firstName = get_user_meta($user_id, 'billing_first_name', true);
                    $ai_lastName = get_user_meta($user_id, 'billing_last_name', true);
                    $billing_company = get_user_meta($user_id, 'billing_company', true);
                    $ai_billing_address1 = get_user_meta($user_id, 'billing_address_1', true);
                    $ai_billing_address2 = get_user_meta($user_id, 'billing_address_2', true);
                    $ai_billing_city = get_user_meta($user_id, 'billing_city', true);
                    $ai_billing_zip = get_user_meta($user_id, 'billing_postcode', true);
                    $ai_billing_country = get_user_meta($user_id, 'billing_country', true);
                    $ai_billing_state = get_user_meta($user_id, 'billing_state', true);
                    $ai_phone = get_user_meta($user_id, 'billing_phone', true);
                    $ai_email = get_user_meta($user_id, 'billing_email', true);

                    
                    $shipping_first_name = get_user_meta($user_id, 'shipping_first_name', true);
                    $shipping_last_name = get_user_meta($user_id, 'shipping_last_name', true);
                    $ai_shipping_address1 = get_user_meta($user_id, 'shipping_address_1', true);
                    $ai_shipping_address2 = get_user_meta($user_id, 'shipping_address_2', true);
                    $ai_shipping_city = get_user_meta($user_id, 'shipping_city', true);
                    $ai_shipping_zip = get_user_meta($user_id, 'shipping_postcode', true);
                    $ai_shipping_country = get_user_meta($user_id, 'shipping_country', true);
                    $ai_shipping_state = get_user_meta($user_id, 'shipping_state', true);

                    // Construct the URL with the parameters
                    $wc_sf_user_url_info = add_query_arg(array(
                        'ai_firstName' => urlencode($ai_firstName),
                        'ai_lastName' => urlencode($ai_lastName),
                        'ai_billing_country' => urlencode($ai_billing_country),
                        'ai_billing_zip' => urlencode($ai_billing_zip),
                        'ai_billing_address1' => urlencode($ai_billing_address1),
                        'ai_billing_address2' => urlencode($ai_billing_address2),
                        'ai_billing_city' => urlencode($ai_billing_city),
                        'ai_billing_state' => urlencode($ai_billing_state),
                        'ai_phone' => urlencode($ai_phone),
                        'ai_email' => urlencode($ai_email)

                        // 'ai_shipping_country' => urlencode($ai_shipping_country),
                        // 'ai_shipping_zip' => urlencode($ai_shipping_zip),
                        // 'ai_shipping_address1' => urlencode($ai_shipping_address1),
                        // 'ai_shipping_address2' => urlencode($ai_shipping_address2),
                        // 'ai_shipping_city' => urlencode($ai_shipping_city),
                        // 'ai_shipping_state' => urlencode($ai_shipping_state)

                    ), ''); // Replace with your actual URL




                }
            }



            $wc_sf_buttom_html.='<a style="color:'.$wc_sf_hpp_button_color.'!important;background-color:'.$wc_sf_hpp_button_bg_color.'!important;text-decoration:none;padding: 5px 20px 5px;"  target="_blank" href="#" data-domain="'.$wc_sf_hpp_link.'" data-urlparam="'.$wc_sf_user_url_info.'" class="wc-sf-varintion-buttons-link">'.esc_html((!empty($wc_sf_variant_hpp_button_text) ? $wc_sf_variant_hpp_button_text : 'Subscribe Now')).'</a>';

            $wc_sf_html.='<div class="wc-sf-variant-hpp-links-section">';
                $wc_sf_html.='<div class="wc-sf-buttons">';
                    $wc_sf_html.=$wc_sf_buttom_html;
                $wc_sf_html.='</div>';
        
            $wc_sf_html.='</div>';

            echo wp_kses_post($wc_sf_html);

        }
       

  
    }
 
    

}

add_action('woocommerce_before_add_to_cart_form','WC_SF_hpp_links_button');
function WC_SF_hpp_links_button() {

   
    if(is_single() && 'product' == get_post_type() ){

        $productID = get_the_ID();
        $wc_sf_meta_repeatable_fields =  get_post_meta($productID,'wc_sf_meta_repeatable_fields',true);

        $wc_sf_hpp_button_text_color=get_option('wc_sf_hpp_button_text_color');
        $wc_sf_hpp_button_bg=get_option('wc_sf_hpp_button_bg');
        $wc_sf_settings_tab_domain=get_option('wc_sf_settings_tab_domain');


        $wc_sf_buttom_html='';
        if(!empty($wc_sf_meta_repeatable_fields )){


                    foreach($wc_sf_meta_repeatable_fields as $single_repeatable_fields){

                        if(!empty($single_repeatable_fields['wc_sf_hpp_plan_id_name'])){

                            $wc_sf_plan_id = substr($single_repeatable_fields['wc_sf_hpp_plan_id_name'],0, strpos($single_repeatable_fields['wc_sf_hpp_plan_id_name'], "|"));

                            // esc_url
                            $wc_sf_hpp_link=$wc_sf_settings_tab_domain.'/en/hosted-page/subscribe/'. $wc_sf_plan_id.'/product/';
                            if(!empty($single_repeatable_fields['wc_sf_hpp_product_id']))
                                $wc_sf_hpp_link.=$single_repeatable_fields['wc_sf_hpp_product_id'];

                            //Button text
                            $wc_sf_hpp_button_text=esc_html('Subscribe Now');
                            if(!empty($single_repeatable_fields['wc_sf_hpp_button_text']))
                                $wc_sf_hpp_button_text=$single_repeatable_fields['wc_sf_hpp_button_text'];

                        
                            //Button style
                            $wc_sf_hpp_button_color='#fff';
                            if(!empty($wc_sf_hpp_button_text_color))
                                $wc_sf_hpp_button_color=$wc_sf_hpp_button_text_color;

                            $wc_sf_hpp_button_bg_color='#fd7b03';
                            if(!empty($wc_sf_hpp_button_bg))
                                $wc_sf_hpp_button_bg_color=$wc_sf_hpp_button_bg;

                            $wc_sf_buttom_html.='<a style="color:'.$wc_sf_hpp_button_color.'!important;background-color:'.$wc_sf_hpp_button_bg_color.'!important;text-decoration:none;" target="_blank" href="'.esc_url_raw($wc_sf_hpp_link).'" class="wc-sf-buttons-link ">'.esc_html($wc_sf_hpp_button_text).'</a>';
                        }
                    
                    }   

        }

        $wc_sf_html='';

        if(!empty($wc_sf_buttom_html)){

            $wc_sf_html.='<div class="wc-sf-hpp-links-section">';
                $wc_sf_html.='<div class="wc-sf-buttons">';
                    $wc_sf_html.=$wc_sf_buttom_html;
                $wc_sf_html.='</div>';
            
            $wc_sf_html.='</div>';
        }
        

    
        echo wp_kses_post($wc_sf_html);
    }

    
  
    

}