<?php

/**
 * 
 * Customer Route
 * 
 * Set in SF Webhook
 * 
 * Webhook: http://example.com/wp-json/api/sf-create-update-user?customer_id=e4f505c4-4af8-4c87-b938-7462216edeef
 * 
*/

add_action('rest_api_init', function () {
	register_rest_route( 'api', 'sf-create-update-user',array(
				  'methods'  => 'GET',
				  'callback' => 'WC_SF_creat_customer_webhook',
				  'permission_callback' => '__return_true'
		));
  });



function WC_SF_creat_customer_webhook($request) {


	
	$wc_sf_response_logs=array();

	if(!empty($request['customer_id'])){

	
		$SF_domanin_url= get_option('wc_sf_settings_tab_domain'); 

		$wc_sf_helper_class_obj = WC_SF_Helper::WC_SF_helper_object();
		$token_response_msg = $wc_sf_helper_class_obj->WC_SF_api_get_token();
	

		if($token_response_msg->status){

			if(!empty($token_response_msg->data->access_token)){

				$access_token=$token_response_msg->data->access_token;
				$get_customer_detail_req = $wc_sf_helper_class_obj->WC_SF_curl_get_sf_request($SF_domanin_url.'/api/v1/customers/'.$request['customer_id'],$access_token,'GET');



				if(!empty($get_customer_detail_req['res_data']['data']['attributes'])){

					$customer_detail=$get_customer_detail_req['res_data']['data']['attributes'];
					$userdata=array();

					/**
					 * 
					 * Get user by SF customer ID
					 * 
					*/

					$get_user_by_key_val=get_users(array(
						'meta_key' => 'wc_sf_customer_id',
						'meta_value' => $request['customer_id']
					));

				


					//Check user exist
					$email=(!empty($customer_detail['email']) ? $customer_detail['email']  : '');
					$user_exists = email_exists($email);

	
					if(!empty($user_exists))
						$userdata['ID']= $user_exists;
					else
						$userdata['ID']= (!empty($get_user_by_key_val[0]->ID) ? $get_user_by_key_val[0]->ID : '');;

					$user_pass=wp_generate_password( 8, false );


					if(!empty($customer_detail['name'])){
						$userdata['user_nicename']=sanitize_text_field($customer_detail['name']);
						$userdata['display_name']=sanitize_text_field($customer_detail['name']);
						$userdata['first_name']=sanitize_text_field($customer_detail['name']);
					}
					if(!empty($customer_detail['email'])){
						$userdata['user_login']=sanitize_email($customer_detail['email']);
						$userdata['user_email']=sanitize_email($customer_detail['email']);
					}
			
					if(!$user_exists && (empty($get_user_by_key_val[0]->ID)))
						$userdata['user_pass']= $user_pass;
						
					$userdata['role']= 'customer';


					$user_id = wp_insert_user( $userdata ) ;

					if ( is_wp_error( $user_id ) ) {

						$error_message =$user_id->get_error_message();

						$wc_sf_response_logs['status']=false;
						$wc_sf_response_logs['res_data']=$error_message;
						$wc_sf_response_logs['created_at']=date('Y-m-d H:i:s');
						
					}
					else{

						/**
						 * 
						 * Billing fields
						 * 
						*/

						if(!empty($customer_detail['name'])){
							update_usermeta($user_id,'billing_first_name',sanitize_text_field($customer_detail['name']));
						}
					
						if(!empty($customer_detail['billing_address_1'])){
							update_usermeta($user_id,'billing_address_1',sanitize_text_field($customer_detail['billing_address_1']));
						}
						
						if(!empty($customer_detail['billing_address_2'])){
							update_usermeta($user_id,'billing_address_2',sanitize_text_field($customer_detail['billing_address_2']));
						}
						if(!empty($customer_detail['billing_city'])){
							update_usermeta($user_id,'billing_city',sanitize_text_field($customer_detail['billing_city']));
						}
						
						if(!empty($customer_detail['billing_postal_code'])){
							update_usermeta($user_id,'billing_postcode',sanitize_text_field($customer_detail['billing_postal_code']));
						}

						if(!empty($customer_detail['billing_country'])){
							update_usermeta($user_id,'billing_country',sanitize_text_field($customer_detail['billing_country']));
						}

						if(!empty($customer_detail['billing_state'])){
							update_usermeta($user_id,'billing_state',sanitize_text_field($customer_detail['billing_state']));
						}
		
						if(!empty($customer_detail['email'])){
							update_usermeta($user_id,'billing_email',sanitize_email($customer_detail['email']));
						}

						if(!empty($customer_detail['phone_number'])){
							update_usermeta($user_id,'billing_phone',sanitize_text_field($customer_detail['phone_number']));
						}

						/**
						 * 
						 * Shipping fields
						 * 
						 * 
						*/

						if(!empty($customer_detail['name'])){
							update_usermeta($user_id,'shipping_first_name',sanitize_text_field($customer_detail['name']));
						}
						
						if(!empty($customer_detail['shipping_address_1'])){
							update_usermeta($user_id,'shipping_address_1',sanitize_text_field($customer_detail['shipping_address_1']));
						}
						
						if(!empty($customer_detail['shipping_address_2'])){
							update_usermeta($user_id,'shipping_address_2',sanitize_text_field($customer_detail['shipping_address_2']));
						}
						if(!empty($customer_detail['shipping_city'])){
							update_usermeta($user_id,'shipping_city',sanitize_text_field($customer_detail['shipping_city']));
						}
						
						if(!empty($customer_detail['shipping_postal_code'])){
							update_usermeta($user_id,'shipping_postcode',sanitize_text_field($customer_detail['shipping_postal_code']));
						}
						
						if(!empty($customer_detail['shipping_country'])){
							update_usermeta($user_id,'shipping_country',sanitize_text_field($customer_detail['shipping_country']));
						}
						
						if(!empty($customer_detail['shipping_state'])){
							update_usermeta($user_id,'shipping_state',sanitize_text_field($customer_detail['shipping_state']));
						}
						if(!empty($customer_detail['phone_number'])){
							update_usermeta($user_id,'shipping_phone',sanitize_text_field($customer_detail['phone_number']));
						}
						
						if(!empty($customer_detail['email'])){
							update_usermeta($user_id,'shipping_email',sanitize_email($customer_detail['email']));
						}
						

						if(!empty($customer_detail['id']))
							update_user_meta($user_id,'wc_sf_customer_id', sanitize_key($customer_detail['id']));

						/**
						 * 
						 *  Update customer back to sf
						 *
						*/

						$sf_data_array=array();

						$sf_data_array['wp_user']='Yes';
						$sf_data_array['wp_temp_pass']=$user_pass;
						$sf_data_array['wp_password']=$user_pass;
						$res=$wc_sf_helper_class_obj->WC_SF_curl_post_domain_form_request($sf_data_array,$SF_domanin_url.'/api/v1/customers/'.$request['customer_id'], $access_token,'PUT');

						$wc_sf_response_logs['status']=true;
						$wc_sf_response_logs['res_data']="WP User has been created ID : ". $user_id;
						$wc_sf_response_logs['created_at']=date('Y-m-d H:i:s');
			
					}
						


				
				}
				else{
				
					$wc_sf_response_logs['status']=false;

					if(!empty($get_customer_detail_req['res_data']))
						$wc_sf_response_logs['res_data']=$get_customer_detail_req['res_data'];
					else
						$wc_sf_response_logs['res_data']='No record found!';

					$wc_sf_response_logs['created_at']=date('Y-m-d H:i:s');
					
				}

			}
		

		}
								
		$wc_sf_helper_class_obj->WC_SF_api_logs('-----------------WC_SF_creat_customer_webhook triger---------------');
		$wc_sf_helper_class_obj->WC_SF_api_logs(json_encode($wc_sf_response_logs));
		$wc_sf_helper_class_obj->WC_SF_api_logs('-----------------WC_SF_creat_customer_webhook triger end---------------');
		$wc_sf_helper_class_obj->WC_SF_api_logs(' ');

		return wp_send_json($wc_sf_response_logs);


	
	}


   
}



/**
 * 
 * Send Customer request from Web to SF
 * 
*/
add_action( 'user_register', 'WC_SF_registration_save', 10, 1 );
//add_action( 'profile_update', 'WC_SF_registration_save', 10, 1 );

function WC_SF_registration_save( $user_id ) {
 
	$wc_sf_settings_add_custom_sf=get_option('wc_sf_settings_add_custom_sf');

	if(!empty($wc_sf_settings_add_custom_sf)){

		if($wc_sf_settings_add_custom_sf=='yes'){

			$user_info = get_userdata($user_id);

			$user_roles = $user_info->roles;


			$username = $user_info->user_login;
			$first_name = $user_info->first_name;
			$last_name = $user_info->last_name;
			$user_name = $user_info->display_name;
			$user_email = $user_info->user_email;
			$wc_sf_check_customer_id=get_user_meta($user_id,'wc_sf_customer_id',true);


			$wc_sf_helper_class_obj = WC_SF_Helper::WC_SF_helper_object();
			$token_response_msg = $wc_sf_helper_class_obj->WC_SF_api_get_token();

			$SF_domanin_url=get_option('wc_sf_settings_tab_domain');
			
			
			if($token_response_msg->status && in_array( 'customer', $user_roles, true )){

				if(!empty($token_response_msg->data->access_token)){

					$access_token=$token_response_msg->data->access_token;

				
					$sf_data_array=array();

					$sf_data_array['name']=$first_name.' '.$last_name;
					$sf_data_array['email']=$user_email;
					$sf_data_array['wp_user']='Yes';
					$sf_data_array['portal_is_enabled']=1;


					if(!empty($wc_sf_check_customer_id)){

						$seach_record_res_arr = $wc_sf_helper_class_obj->WC_SF_curl_get_sf_request($SF_domanin_url.'/api/v1/customers/'.$wc_sf_check_customer_id,$access_token,'GET');

						if(!empty($seach_record_res_arr['res_data']['data']['id']))
							$wc_sf_check_customer_id= $seach_record_res_arr['res_data']['data']['id'];
				
						

						
					}
					else{


						$seach_record_res_arr = $wc_sf_helper_class_obj->WC_SF_curl_get_sf_request($SF_domanin_url.'/api/v1/customers/filter?filter[email][$equals]=' . $user_email,$access_token);

						if(!empty($seach_record_res_arr['res_data']['data'][0]['id']))
							$wc_sf_check_customer_id=$seach_record_res_arr['res_data']['data'][0]['id'];
					}




					if(!empty($wc_sf_check_customer_id)){

						/**
						 * 
						 * User billing address mapping
						 * 
						*/
						$wc_sf_user_billing_address_1 = get_user_meta($user_id,'billing_address_1',true);
						$wc_sf_user_billing_address_2 = get_user_meta($user_id,'billing_address_2',true);
						$wc_sf_user_billing_city = get_user_meta($user_id,'billing_city',true);
						$wc_sf_user_billing_state = get_user_meta($user_id,'billing_state',true);
						$wc_sf_user_billing_country = get_user_meta($user_id,'billing_country',true);
						$wc_sf_user_billing_postcode = get_user_meta($user_id,'billing_postcode',true);
						
			
						$sf_data_array['billing_address_1']=$wc_sf_user_billing_address_1;
						$sf_data_array['billing_address_2']=$wc_sf_user_billing_address_2;
						$sf_data_array['billing_city']=$wc_sf_user_billing_city;
						$sf_data_array['billing_state']=$wc_sf_user_billing_state ;
						$sf_data_array['billing_postal_code']=$wc_sf_user_billing_postcode;
						$sf_data_array['billing_country']=$wc_sf_user_billing_country;


						/**
						 * 
						 * User shipping address mapping
						 * 
						*/
						$wc_sf_user_shipping_address_1 = get_user_meta($user_id,'shipping_address_1',true);
						$wc_sf_user_shipping_address_2 = get_user_meta($user_id,'shipping_address_2',true);
						$wc_sf_user_shipping_city = get_user_meta($user_id,'shipping_city',true);
						$wc_sf_user_shipping_state = get_user_meta($user_id,'shipping_state',true);
						$wc_sf_user_shipping_country = get_user_meta($user_id,'shipping_country',true);
						$wc_sf_user_shipping_postcode = get_user_meta($user_id,'shipping_postcode',true);
						
			
						$sf_data_array['shipping_address_1']=$wc_sf_user_shipping_address_1;
						$sf_data_array['shipping_address_2']=$wc_sf_user_shipping_address_2;
						$sf_data_array['shipping_city']=$wc_sf_user_shipping_city;
						$sf_data_array['shipping_state']=$wc_sf_user_shipping_state ;
						$sf_data_array['shipping_postal_code']=$wc_sf_user_shipping_postcode;
						$sf_data_array['shipping_country']=$wc_sf_user_billing_country;
				

						$wc_sf_update_customer_res_arr=$wc_sf_helper_class_obj->WC_SF_curl_post_domain_form_request($sf_data_array,$SF_domanin_url.'/api/v1/customers/'.$wc_sf_check_customer_id, $access_token,'PUT');
						
						if(!empty($wc_sf_update_customer_res_arr['res_data']['data']['id']))
							update_user_meta($user_id,'wc_sf_customer_id', sanitize_key($wc_sf_update_customer_res_arr['res_data']['data']['id']));

					}
					else{

						$wc_sf_add_customer_res_arr=$wc_sf_helper_class_obj->WC_SF_curl_post_domain_form_request($sf_data_array,$SF_domanin_url.'/api/v1/customers/', $access_token,'POST');


						if(!empty($wc_sf_add_customer_res_arr['res_data']['data']['id']))
							update_user_meta($user_id,'wc_sf_customer_id', sanitize_key($wc_sf_add_customer_res_arr['res_data']['data']['id']));

					}
						
				}
			}
		}

	}
	


}