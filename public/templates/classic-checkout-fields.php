<?php
defined('ABSPATH') || exit;
?>
<div class="edp-delivery-wrapper">
	<div class="edp-checkout-order-type">
		<strong>Order Type</strong>
		<select name="edp_checkout_order_type" id="edp_checkout_order_type">
			<option value=""><?php esc_html_e('Select Order Type', 'easy-delivery');?></option>
			<option value="pickup">Pickup</option>
			<option value="delivery">Delivery</option>
		</select>
	</div>
	<div class="edp-checkout-pickup-fields">
		<p class="form-row form-row-wide">
			<label for="edp_checkout_pickup_store"><?php esc_html_e('Pickup Store', 'easy-delivery');?></label>
			<select name="edp_checkout_pickup_store" id="edp_checkout_pickup_store">
				<option value=""><?php esc_html_e('Select Pickup Store', 'easy-delivery');?></option>
				<?php
					foreach($stores as $store):?>
						<option value="<?php echo esc_attr($store->id);?>"
						data-opening-days= '<?php echo esc_attr($store->opening_days);?>'
						data-pickup-time= '<?php echo esc_attr($store->pickup_time);?>'
						data-store-name= '<?php echo esc_attr($store->store_name);?>'
						data-store-address= '<?php echo esc_attr($store->store_address);?>'
						>
						<?php echo esc_html($store->store_name);?>
						</option>
					<?php endforeach;
				?>
			</select>
		</p>
		<p class="form-row form-row-first">
			<label for="edp_checkout_pickup_date"><?php esc_html_e('Pickup Date', 'easy-delivery');?></label>
			<input type="text" name="edp_checkout_pickup_date" id="edp_checkout_pickup_date" value="" readonly>
		</p>
		<p class="form-row form-row-last">
			<label for="edp_checkout_pickup_time"><?php esc_html_e('Pickup Time', 'easy-delivery');?></label>
			<input type="text" name="edp_checkout_pickup_time" id="edp_checkout_pickup_time" value="" readonly>
		</p>
		<!-- Hidden Field -->
		<input type="hidden" name="edp_selected_pickup_store_name" id="edp_selected_pickup_store_name" value="">
		<input type="hidden" name="edp_selected_pickup_store_address" id="edp_selected_pickup_store_address" value="">
	</div>
	<div class="edp-checkout-delivery-fields">
		<p class="form-row form-row-wide">
			<label for="edp_checkout_delivery_store"><?php esc_html_e('Delivery Store', 'easy-delivery');?></label>
			<select name="edp_checkout_delivery_store" id="edp_checkout_delivery_store">
				<option value=""><?php esc_html_e('Select Nearest Store', 'easy-delivery');?></option>
				<?php
					foreach($stores as $store):?>
						<option value="<?php echo esc_attr($store->id);?>"
						data-store-name= '<?php echo esc_attr($store->store_name);?>'
						data-opening-days= '<?php echo esc_attr($store->opening_days);?>'
						>
						<?php echo esc_html($store->store_name);?>
						</option>
					<?php endforeach;
				?>
			</select>
		</p>
		<p class="form-row form-row-wide">
			<label for="edp_checkout_delivery_date"><?php esc_html_e('Delivery Date', 'easy-delivery');?></label>
			<input type="text" name="edp_checkout_delivery_date" id="edp_checkout_delivery_date" value="" readonly>
		</p>
		<!-- Hidden field -->
		<input type="hidden" name="edp_selected_delivery_store_name" id="edp_selected_delivery_store_name" value="">
	</div>
</div>