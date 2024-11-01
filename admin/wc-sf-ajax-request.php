<?php
defined( 'ABSPATH' ) || exit;

/**
 * 
 * Send plans in reponse against selected product
 * 
*/
add_action("wp_ajax_wc_sf_get_product_plan", "WC_SF_get_product_plan");
add_action("wp_ajax_nopriv_wc_sf_get_product_plan", "WC_SF_get_product_plan");

function WC_SF_get_product_plan() {



      $wc_sf_product_id=esc_html($_REQUEST['wc_sf_product_id']);
      $wc_sf_helper_class_obj = WC_SF_Helper::WC_SF_helper_object();

		$wc_sf_get_access_token_obj=$wc_sf_helper_class_obj->WC_SF_api_get_token();
		$wc_sf_get_access_token=$wc_sf_get_access_token_obj->data->access_token;

		$url=$wc_sf_helper_class_obj->domain_URL.'/api/'.WC_SF_API_V.'/products/'.$wc_sf_product_id.'/link/plans';

		$wc_sf_product_list=$wc_sf_helper_class_obj->WC_SF_curl_get_sf_request($url, $wc_sf_get_access_token);
      
      $html='';
            if(!empty($wc_sf_product_list['res_data']['data'])):

               //$html.='<select class="widefat" name="wc_sf_hpp_plan_id_name[]">';
                  $html.='<option value="">'.esc_attr('Select plan').'</option>';
                  foreach($wc_sf_product_list['res_data']['data']  as $wc_sf_single_product): 

                     $wc_sf_single_product_val='';
                     if(!empty($wc_sf_single_product['id']) && !empty($wc_sf_single_product['name']))
                        $wc_sf_single_product_val=$wc_sf_single_product['id'] .'|'.$wc_sf_single_product['name'];
             


                     $html.='<option value="'.esc_attr($wc_sf_single_product_val).'" >'.esc_attr((!empty($wc_sf_single_product['name']) ? $wc_sf_single_product['name'] : '')).'</option>';
                  endforeach;
            else:
               $html.='<option value="">'.esc_html("Sorry,No plan found!").'</option>';
               //$html.='</select>';
           
            endif;
      
      echo wp_send_json(array('res_data' => $html,'status'=>true));
 

   die();

}


/**
 * 
 * Send plans in reponse against selected product
 * 
*/
add_action("wp_ajax_wc_sf_sync_product", "WC_SF_sync_product");
add_action("wp_ajax_nopriv_wc_sf_sync_product", "WC_SF_sync_product");

function WC_SF_sync_product() {


      $html='';

      $wc_sf_helper_class_obj = WC_SF_Helper::WC_SF_helper_object();
	
      $wc_sf_get_access_token_obj=$wc_sf_helper_class_obj->WC_SF_api_get_token();
      $wc_sf_get_access_token=(!empty($wc_sf_get_access_token_obj->data->access_token)) ?$wc_sf_get_access_token_obj->data->access_token : '';

      $url=$wc_sf_helper_class_obj->domain_URL.'/api/'.WC_SF_API_V.'/products';

      $wc_sf_product_list=$wc_sf_helper_class_obj->WC_SF_curl_get_sf_request($url, $wc_sf_get_access_token);


      if(!empty($wc_sf_product_list['res_data']['data'])){

         $wc_sf_plan_data=array();
         $wc_sf_plan_data['data'][]=$wc_sf_product_list['res_data']['data'];
         $wc_sf_plan_data['date']=date('Y-m-d H:i:s');
         
         update_option('wc_sf_product_data',json_encode($wc_sf_plan_data));

         $html='<span class="notice notice-success is-dismissible wc-sf-msg">'.esc_html("Your data has been successfully updated").'</span>';
      }
      else{

         $html='<span class="notice notice-error is-dismissible wc-sf-msg">'.esc_html("Something went wrong. Please try after sometime").'</span>';
      }

    
      echo wp_send_json(array('res_data' => $html,'status'=>true));
   

      die();

}

