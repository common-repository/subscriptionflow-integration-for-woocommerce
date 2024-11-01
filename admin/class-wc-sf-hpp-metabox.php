<?php

defined( 'ABSPATH' ) || exit;

/**
 * 
 * Metabox class
 * 
 * On Product edit page
 * 
 * Map Products with Plans
 * 
 * 
*/
class WC_SF_HPP_Metabox {


	public static function WC_SF_repeater_fields_add_meta_boxes() {

		add_meta_box( 'wc_sf_repeatable-fields', 'SubscriptionFlow HPP Links', array( self::class, 'WC_SF_repeatable_meta_box_display' ), 'product', 'normal', 'default');
	
	}

	/**
	 * 
	 * Display the meta box HTML to the user.
	 *
	 */

	public static function WC_SF_repeatable_meta_box_display() {

		global $post;
	
		$wc_sf_meta_repeatable_fields = get_post_meta($post->ID, 'wc_sf_meta_repeatable_fields', true);
		$wc_sf_hide_cart_button = get_post_meta($post->ID, 'wc_sf_hide_cart_button', true);

		$wc_sf_handle_checkout_product_id = get_post_meta($post->ID, 'wc_sf_handle_checkout_product_id', true);
		$wc_sf_handle_checkout_plan_id_name = get_post_meta($post->ID, 'wc_sf_handle_checkout_plan_id_name', true);
	
	
		wp_nonce_field( 'wc_sf_repeatable_meta_box_nonce', 'wc_sf_repeatable_meta_box_nonce' );
	
		?>
	
		<table id="wc-sf-repeatable-fieldset-one" width="100%">
			<thead>
				<tr>
					<th width="15%"><?php echo esc_attr( 'Button Text'); ?> </th>
					<th width="35%"><?php echo esc_attr( 'Select Product'); ?> </th>
					<th width="40%"><?php echo esc_attr( 'Select Plan'); ?></th>
					<th width="10%"></th>
				</tr>
			</thead>
			<tbody>
			<?php
	
			/**
			 * 
			 * Get SF Product from Webshop DB
			 * 
			*/
			$wc_sf_product_list=get_option('wc_sf_product_data');

			if(!empty($wc_sf_product_list)){
				$wc_sf_product_list=json_decode($wc_sf_product_list,true);
			}	

		
		
			if ( $wc_sf_meta_repeatable_fields ) :
			
				foreach ( $wc_sf_meta_repeatable_fields as $field ) {
				?>
				<tr>
					<td><input type="text" class="widefat" name="wc_sf_hpp_button_text[]" value="<?php esc_html_e((!empty($field['wc_sf_hpp_button_text']) ? $field['wc_sf_hpp_button_text'] : '')); ?>" /></td>
	
					<td>
						<select class="widefat wc-sf-product-list-dropdown" name="wc_sf_hpp_product_id[]">
							<option value=""><?php esc_html_e( 'Select product'); ?></option>

							<?php if(!empty($wc_sf_product_list['data'])): ?>

								<?php foreach($wc_sf_product_list['data']  as $wc_sf_product_arr): ?>
								
									<?php foreach($wc_sf_product_arr  as $wc_sf_single_product): ?>

											<option value="<?php esc_html_e((!empty($wc_sf_single_product['id']) ? $wc_sf_single_product['id'] : '')); ?>" <?php if($wc_sf_single_product['id']==$field['wc_sf_hpp_product_id']) esc_html_e('selected'); ?>><?php esc_html_e((!empty($wc_sf_single_product['name']) ? $wc_sf_single_product['name'] : '')); ?></option>
											
									<?php endforeach;?>
								<?php endforeach;?>
							<?php else: ?>
								<option><?php esc_html("Please sync products from setting tab") ?></option>
							<?php endif; ?>
						</select>
					</td>
					<?php if(!empty($field['wc_sf_hpp_plan_id_name'])): ?>
						<?php 
						
							$plan_name = substr($field['wc_sf_hpp_plan_id_name'], strpos($field['wc_sf_hpp_plan_id_name'], "|") + 1);
						?>
						<td class="product-plan"><select class="widefat wc-sf-product-list-dropdown" name="wc_sf_hpp_plan_id_name[]"><option value="<?php esc_html_e($field['wc_sf_hpp_plan_id_name']); ?>"><?php esc_html_e($plan_name); ?></option></select></td>
					<?php else: ?>
						<td class="product-plan"><select class="widefat wc-sf-product-list-dropdown" name="wc_sf_hpp_plan_id_name[]"><option><?php esc_html_e( 'Please select product'); ?></option></select></td>
					<?php endif; ?>
					<td><a class="button remove-row" href="#"><?php esc_html_e( 'Remove'); ?></a></td>
				</tr>
				<?php
				}
				
			else :
			// show a blank one
			?>
			
				<tr>
					<td><input type="text" class="widefat" name="wc_sf_hpp_button_text[]" /></td>
					<td>
						<select class="widefat wc-sf-product-list-dropdown"  name="wc_sf_hpp_product_id[]">
							<option value=""><?php  esc_html_e('Select Product') ?></option>
							<?php if(!empty($wc_sf_product_list['data'])): ?>
								
									<?php foreach($wc_sf_product_list['data']  as $wc_sf_product_arr): ?>
								
										<?php foreach($wc_sf_product_arr  as $wc_sf_single_product): ?>

											<option value="<?php esc_html_e((!empty($wc_sf_single_product['id']) ? $wc_sf_single_product['id'] : '')); ?>" ><?php esc_html_e((!empty($wc_sf_single_product['name']) ? $wc_sf_single_product['name'] : '')); ?></option>
											
										<?php endforeach;?>
									<?php endforeach;?>
							<?php else: ?>
								<option><?php esc_html("Please sync products from setting tab") ?></option>
							<?php endif; ?>
						</select>
					</td>
					<td class="product-plan"><select class="widefat wc-sf-product-list-dropdown" name="wc_sf_hpp_plan_id_name[]"><option><?php echo esc_attr( 'Please select product'); ?></option></select></td>
					<td><a class="button remove-row" href="#"><?php echo esc_attr( 'Remove'); ?></a></td>
				</tr>
	
			<?php endif; ?>
			
			<!-- empty hidden one for jQuery -->
			<tr class="empty-row screen-reader-text">
				<td><input type="text" class="widefat" name="wc_sf_hpp_button_text[]" /></td>
				<td>
					<select class="widefat wc-sf-product-list-dropdown" name="wc_sf_hpp_product_id[]">
						<option value=""><?php echo esc_attr( 'Select Product'); ?></option>
						<?php if(!empty($wc_sf_product_list['data'])): ?>
							
								<?php foreach($wc_sf_product_list['data']  as $wc_sf_product_arr): ?>
								
									<?php foreach($wc_sf_product_arr  as $wc_sf_single_product): ?>

										<option value="<?php esc_html_e((!empty($wc_sf_single_product['id']) ? $wc_sf_single_product['id'] : '')); ?>" ><?php esc_html_e((!empty($wc_sf_single_product['name']) ? $wc_sf_single_product['name'] : '')); ?></option>
										
									<?php endforeach;?>
								<?php endforeach;?>
						<?php else: ?>
							<option><?php esc_html("Please sync products from setting tab") ?></option>
						<?php endif; ?>
					</select>
				</td>
				<td class="product-plan"><select class="widefat wc-sf-product-list-dropdown" name="wc_sf_hpp_plan_id_name[]"><option><?php echo esc_attr( 'Please select product'); ?></option></select></td>
				<td><a class="button remove-row" href="#"><?php echo esc_attr( 'Remove'); ?></a></td>
			</tr>
	
			
				
			</tbody>
		</table>
		
		
		<p><a id="add-row" class="button" href="#"><?php echo esc_attr( 'Add another'); ?></a></p>
	
		<div class="wc-sf-single-product-other-option">

			<p>
				<input type="checkbox" value="yes" name="wc_sf_hide_cart_button" <?php esc_html_e((($wc_sf_hide_cart_button=='yes') ? 'checked' : '')); ?>><?php echo esc_attr( 'Do you want to hide Cart button?'); ?>
			</p>
		
		</div>
		<div class="postbox-header"><h2 class="hndle ui-sortable-handle"></h2></div>
		<div class="wc-sf-single-product-other-option">
			<h2 class="wc-sf-section-title">Enable SubscriptionFlow Checkout Button(For Simple Product Only)</h2>

			<table id="sf-checkout-opt" width="100%">
				<thead>
					<tr>
						<th width="35%"><?php echo esc_attr( 'Select Product'); ?> </th>
						<th width="40%"><?php echo esc_attr( 'Select Plan'); ?></th>
						<th width="10%"></th>
					</tr>
				</thead>
				<tr>

					<td>
						<select class="widefat wc-sf-product-list-dropdown" name="wc_sf_handle_checkout_product_id">
							<option value=""><?php echo esc_attr( 'Select product'); ?></option>
							<?php if(!empty($wc_sf_product_list['data'])): ?>
									
									<?php foreach($wc_sf_product_list['data']  as $wc_sf_product_arr): ?>
								
										<?php foreach($wc_sf_product_arr  as $wc_sf_single_product): ?>

											<option value="<?php esc_html_e((!empty($wc_sf_single_product['id']) ? $wc_sf_single_product['id'] : '')); ?>" <?php if($wc_sf_single_product['id']==$wc_sf_handle_checkout_product_id) esc_html_e('selected'); ?>><?php esc_html_e((!empty($wc_sf_single_product['name']) ? $wc_sf_single_product['name'] :'')); ?></option>
										
										<?php endforeach;?>
									<?php endforeach;?>
							<?php else: ?>
								<option><?php esc_html("Please sync products from setting tab") ?></option>
							<?php endif; ?>
						</select>
					</td>

					<td class="product-plan">
						
						<?php if(!empty($wc_sf_handle_checkout_plan_id_name)): ?>
							<?php 
							
								$plan_name = substr($wc_sf_handle_checkout_plan_id_name, strpos($wc_sf_handle_checkout_plan_id_name, "|") + 1);
							?>
								<select class="widefat wc-sf-product-list-dropdown" name="wc_sf_handle_checkout_plan_id_name">
									
									<option value="<?php esc_html_e($wc_sf_handle_checkout_plan_id_name); ?>"><?php esc_html_e($plan_name); ?></option>
							</select>
						<?php else: ?>
							<select class="widefat wc-sf-product-list-dropdown" name="wc_sf_handle_checkout_plan_id_name"><option value=""><?php esc_html_e( 'Please select product'); ?></option></select>
						<?php endif; ?>

					</td>

				</tr>
			</table>
		</div>


				
		<?php
	}


	/**
	 * Save the meta box selections.
	 *
	 * @param int $post_id  The post ID.
	 */
	public static function WC_SF_repeatable_meta_box_save($post_id) {


		if ( ! isset( $_POST['wc_sf_repeatable_meta_box_nonce'] ) ||
		! wp_verify_nonce( $_POST['wc_sf_repeatable_meta_box_nonce'], 'wc_sf_repeatable_meta_box_nonce' ) )
			return;
		
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;
		
		if (!current_user_can('edit_post', $post_id))
			return;
		
		$old = get_post_meta($post_id, 'wc_sf_meta_repeatable_fields', true);
		$new = array();
	
		$WC_SF_Helper_Object=WC_SF_Helper::WC_SF_helper_object();

		$wc_sf_hpp_button_text =$WC_SF_Helper_Object->WC_SF_meta_box_sanitize($_POST['wc_sf_hpp_button_text'],'wc_sf_hpp_button_text');
		$wc_sf_hpp_product_id =$WC_SF_Helper_Object->WC_SF_meta_box_sanitize($_POST['wc_sf_hpp_product_id'],'wc_sf_hpp_product_id');
		$wc_sf_hpp_plan_id_name =$WC_SF_Helper_Object->WC_SF_meta_box_sanitize($_POST['wc_sf_hpp_plan_id_name'],'wc_sf_hpp_plan_id_name');


		$count = count( $wc_sf_hpp_button_text );
		
		for ( $i = 0; $i < $count; $i++ ) {
	
			if ( $wc_sf_hpp_button_text[$i] != '' ) :
				$new[$i]['wc_sf_hpp_button_text'] = stripslashes( strip_tags( $wc_sf_hpp_button_text[$i] ) );
				$new[$i]['wc_sf_hpp_product_id'] =stripslashes( $wc_sf_hpp_product_id[$i] );
				$new[$i]['wc_sf_hpp_plan_id_name'] =stripslashes( $wc_sf_hpp_plan_id_name[$i] );		
			endif;
		}
	
	
		if ( !empty( $new ) )
			update_post_meta( $post_id, 'wc_sf_meta_repeatable_fields', $new );

		else
			delete_post_meta($post_id, 'wc_sf_meta_repeatable_fields'); 

			
			$wc_sf_hide_cart_button = sanitize_text_field($_POST['wc_sf_hide_cart_button']);

			
			if(empty($wc_sf_hide_cart_button))
				$wc_sf_hide_cart_button ='no';

			update_post_meta( $post_id, 'wc_sf_hide_cart_button', $wc_sf_hide_cart_button );


			//Enable cart button


				

			if(!empty($_POST['wc_sf_handle_checkout_plan_id_name']))
				update_post_meta( $post_id, 'wc_sf_handle_checkout_plan_id_name',sanitize_text_field($_POST['wc_sf_handle_checkout_plan_id_name']));
	
				
			if(!empty($_POST['wc_sf_handle_checkout_product_id'])){

				update_post_meta( $post_id, 'wc_sf_handle_checkout_product_id',sanitize_key($_POST['wc_sf_handle_checkout_product_id']) );

			}
			else{
				delete_post_meta($post_id, 'wc_sf_handle_checkout_product_id'); 
				delete_post_meta($post_id, 'wc_sf_handle_checkout_plan_id_name'); 
			}




	
	}
}

add_action( 'add_meta_boxes', array('WC_SF_HPP_Metabox', 'WC_SF_repeater_fields_add_meta_boxes') );
add_action( 'save_post', array( 'WC_SF_HPP_Metabox', 'WC_SF_repeatable_meta_box_save') );


