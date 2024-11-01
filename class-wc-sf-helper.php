<?php

if ( ! class_exists( 'WC_SF_Helper' ) ) :

    class WC_SF_Helper{


        private static $obj;
        public $domain_URL,$client_id,$client_secret;

        private function __construct() {

            $this->domain_URL=get_option('wc_sf_settings_tab_domain'); 
            $this->client_id=get_option('wc_sf_settings_tab_client_id'); 
            $this->client_secret=get_option('wc_sf_settings_tab_client_secret'); 
            
            
        }


        public static function WC_SF_helper_object() {

            if (!isset(self::$obj)) {
                self::$obj = new WC_SF_Helper();
            }
             
            return self::$obj;
        }

        /**
         * 
         * @@ function to generate access_token
         * 
        **/

        public function WC_SF_generate_token_function()
        {

    
            $message=array();

            try {
                
                $wc_sf_token_response = wp_remote_post( $this->domain_URL.'/oauth/token', array(
                    'method'      => 'POST',
                    'timeout'     => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(),
                    'body'        => array(
                        'client_id' => $this->client_id,
                        'client_secret' => $this->client_secret,
                        'grant_type' => 'client_credentials'
                    ),
                    'cookies'     => array()
                    )
                );
                

                if ( is_wp_error( $wc_sf_token_response ) ) {

                    $error_message = $wc_sf_token_response->get_error_message();

                    $message['status']=false;
                    $message['data']=$error_message;
                    $message['created_at']=date('Y-m-d H:i:s');

                

                } else {

                    $wc_sf_token_response_arr = json_decode(wp_remote_retrieve_body($wc_sf_token_response), true);

                    if(!empty($wc_sf_token_response_arr['access_token'])){
                        $message['status']=true;
                        $message['data']=$wc_sf_token_response_arr;
                        $message['created_at']=date('Y-m-d H:i:s');

                    }
                    else{

                        $message['status']=false;
                        $message['data']=(!empty($wc_sf_token_response_arr['message'])) ? $wc_sf_token_response_arr['message'] : 'Looks like you made a mistake!';
                        $message['created_at']=date('Y-m-d H:i:s');

                    }
         
                   
                    
                    
                }
                
            } 
            catch (Exception $e) {
                
                $message['status']=false;
                $message['data']=$e->getMessage();
                $message['created_at']=date('Y-m-d H:i:s');
            }
            


            //Store log incase api fail...
            if(!$message['status']){

                self::WC_SF_api_logs('-----------------Authentication error---------------');
                self::WC_SF_api_logs(json_encode( $message));
                self::WC_SF_api_logs('-----------------Authentication error end---------------');
                self::WC_SF_api_logs(' ');
            }
   

          
            return $message;
        
        }

        /**
         * 
         * @@ Store token into file and regenrate after token expiry
         * 
         * 
        */
        public function WC_SF_api_get_token(){
            
            $file_url=plugin_dir_path( __FILE__ )."token.txt";
     
            $json_arr=array();
            if (file_exists($file_url)) {
                $data=file_get_contents($file_url);
                $data_arr=json_decode($data);
                
                
                $mindiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($data_arr->created_at))/60);
                if($mindiff > 45 || !$data_arr->status){

                    $response_msg=self::WC_SF_generate_token_function();	
                    file_put_contents($file_url,json_encode($response_msg));
                    
                }
                    
            }
            else{
                $response_msg=self::WC_SF_generate_token_function();	
                
                file_put_contents($file_url,json_encode($response_msg));
            }

            $data=file_get_contents($file_url);
            $data_arr=json_decode($data);


            return $data_arr;
            
        }


        /**
         * 
         * Call SF get request
         * 
         * @param $url,$access_token,$method
         * 
        */
        public function WC_SF_curl_get_sf_request($url=null, $access_token = null,$method='POST'){

            
            $wc_sf_response_data=array();

            try {
                $wc_sf_data_response = wp_remote_get( $url, array(
                    'headers' => array(
                        'Authorization' => 'Bearer '.$access_token,
                    )
                ) );

                if ( is_wp_error( $wc_sf_data_response ) ) {

                    $error_message = $wc_sf_data_response->get_error_message();
                    $wc_sf_response_data['status']=false;
                    $wc_sf_response_data['url']=$url;
                    $wc_sf_response_data['res_data']=$error_message;
                    $wc_sf_response_data['created_at']=date('Y-m-d H:i:s');

                

                } else {

                    $wc_sf_data_response_arr = json_decode(wp_remote_retrieve_body($wc_sf_data_response), true);

                    if (200 === wp_remote_retrieve_response_code( $wc_sf_data_response ) ) {


                        $wc_sf_response_data['status']=true;
                        $wc_sf_response_data['url']=$url;
                        $wc_sf_response_data['res_data']=$wc_sf_data_response_arr;
                        $wc_sf_response_data['created_at']=date('Y-m-d H:i:s');
                     
                    }
                    else{

                        $wc_sf_response_data['status']=false;
                        $wc_sf_response_data['url']=$url;
                        $wc_sf_response_data['res_data']=(!empty($wc_sf_data_response_arr['message']) ? $wc_sf_data_response_arr['message'] : 'Looks like you made a mistake' );
                        $wc_sf_response_data['created_at']=date('Y-m-d H:i:s');


                    }
                   

                }

              
               
            }
            catch( Exception $ex ) {

                $wc_sf_response_data['status']=false;
                $wc_sf_response_data['url']=$url;
                $wc_sf_response_data['res_data']='Looks like you made a mistake, '.$ex->getMessage();
                $wc_sf_response_data['created_at']=date('Y-m-d H:i:s');
            
            }
            

            //Store log incase api fail...
            if(!$wc_sf_response_data['status']){

                self::WC_SF_api_logs('-----------------Get request api error---------------');
                self::WC_SF_api_logs(json_encode( $wc_sf_response_data));
                self::WC_SF_api_logs('-----------------Get request api error end---------------');
                self::WC_SF_api_logs(' ');
            }
   


            return $wc_sf_response_data;
        }


        /**
         * 
         * @Function to post data in SF
         * 
         * 
        */
        public function WC_SF_curl_post_domain_form_request($all_data=array(),$url=null, $access_token = null,$method='POST')
        {

			$fields_string = '';
            foreach($all_data as $key=>$value) { 
                $fields_string .= $key.'='.$value.'&'; 
            }
            rtrim($fields_string, '&');
			
            $wc_sf_response_data=array();

            try {
                
                $wc_sf_token_response = wp_remote_post($url, array(
                    'method'      => $method,
                    'timeout'     => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(
						  'Authorization' => 'Bearer ' . $access_token
					),
                    'body'        => 	$all_data,
                    'cookies'     => array()
                    )
                );
                

    
     
                if ( is_wp_error( $wc_sf_token_response ) ) {

                    $error_message = $wc_sf_token_response->get_error_message();

                    $wc_sf_response_data['status']=false;
                    $wc_sf_response_data['res_data']=$error_message;
                    $wc_sf_response_data['created_at']=date('Y-m-d H:i:s');

                

                } else {

                    $wc_sf_response_arr = json_decode(wp_remote_retrieve_body($wc_sf_token_response), true);

                    if(!empty($wc_sf_response_arr['data']['id'])){
                        $wc_sf_response_data['status']=true;
                        $wc_sf_response_data['res_data']=$wc_sf_response_arr;
                        $wc_sf_response_data['created_at']=date('Y-m-d H:i:s');

                    }
                    else{

                        $wc_sf_response_data['status']=false;
                        $wc_sf_response_data['res_data']=(!empty($wc_sf_response_arr)) ? $wc_sf_response_arr: 'Looks like you made a mistake!';
                        $wc_sf_response_data['created_at']=date('Y-m-d H:i:s');

                    }
         
                   
                    
                    
                }
                
            } 
            catch (Exception $e) {
                
                $wc_sf_response_data['status']=false;
                $wc_sf_response_data['res_data']=$e->getMessage();
                $wc_sf_response_data['created_at']=date('Y-m-d H:i:s');
            }
            


            //Store log incase api fail...
            if(!$wc_sf_response_data['status']){

                self::WC_SF_api_logs('-----------------Post request api error---------------');
                self::WC_SF_api_logs(json_encode( $wc_sf_response_data));
                self::WC_SF_api_logs('-----------------Post request api error end---------------');
                self::WC_SF_api_logs(' ');
            }
          
            return $wc_sf_response_data;
        
        }

        /**
         * 
         *  @Strore API response to array
         * 
         * 
        */

        public function WC_SF_api_logs($response_msg){
            
            if(empty($response_msg))
                return null;

            $file_url=plugin_dir_path( __FILE__ )."/api_logs.txt";
            file_put_contents($file_url,$response_msg.PHP_EOL , FILE_APPEND | LOCK_EX);
        }
        /**
         * A custom sanitization function that will take the incoming input, and sanitize
         * the input before handing it back to WordPress to save to the database.
         *
         * @since    1.0.0
         *
         * @param    array    $input        The address input.
         * @return   array    $new_input    The sanitized input.
         */
        public function WC_SF_meta_box_sanitize( $input=array(),$key_type='' ) {

            $new_input = array();

            if(!empty($input)){
                
                // Loop through the input and sanitize each of the values
                foreach ( $input as $key => $val ) {

                    if(!empty($val)){

                        switch ( $key_type ) {

                            case 'wc_sf_hpp_button_text':
        
                                $new_input[ $key ] = sanitize_text_field( $val );
                                break;
        
                            case 'wc_sf_hpp_product_id':
        
                                $new_input[ $key ] = sanitize_key( $val );
                                break;
        
                            case 'wc_sf_hpp_plan_id_name':
        
                                $new_input[ $key ] = sanitize_text_field( $val );
                                break;
        
        
                        }
                    }


                }
            }


	        return $new_input;
        }



    }

endif;