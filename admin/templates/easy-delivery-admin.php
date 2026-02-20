<?php

//Prevent From Direct Access

defined('ABSPATH') || exit;

?>
<div class="wrap edp-wrap">
	<form action="options.php" method="post">
		<h1><?php echo esc_html__('Easy Delivery & Pickup Settings', 'easy-delivery')?></h1>
		<div class="nav-tab-wrapper">
			<?php
				foreach($this->tab as $tab_id => $tab_label){
				?>
					<a href="#" data-tab="<?php echo esc_attr($tab_id);?>" class="nav-tab edp-tab <?php echo $tab_id === 'general' ? 'nav-tab-active' : '' ?>"><?php echo esc_html($tab_label)?></a>
				<?php
				} //End oreach Loop
			?>
		</div>
		<?php settings_fields( $this->option_group )?>
		<div class="tab-content" id="tab-general">
			<?php 
				do_settings_sections('easy_delivery_general');
				submit_button('Save Settings');
			?>
		</div>
	</form>
	<div class="tab-content" id="tab-store" style="display:none;">
		<div class="store-detail-wrapper">
			<div class="store-header">
				<h2>Store Details</h2>
				<button type="button" class="button button-primary edp-newStore"><span class="dashicons dashicons-plus-alt2"></span>Add new Store</button>
			</div>
			<table class="widefat fixed striped">
				<thead>
					<th width="5%">ID</th>
					<th width="20%">Store Name</th>
					<th width="20%">Store Address</th>
					<th width="20%">Opening Days</th>
					<th width="10%">Delivery/Pickup Time</th>
					<th width="5%">Delivery Charge</th>
					<th width="10%">Action</th>
				</thead>
				<tbody>
					<?php
						global $wpdb;
						$table_name = "{$wpdb->prefix}edp_store_details";
						$cache_key = 'edp_store_list_admin';
						$query = wp_cache_get($cache_key);
						if( false === $query){
							$query = $wpdb->get_results("SELECT * FROM `". esc_sql($table_name) . "`ORDER BY id DESC");
							wp_cache_set($cache_key, $query);
						}
						if( empty($query)){
							echo '
								<tr>
								<td colspan="7" style="text-align:center";> No Store Found. Click "Add New Store" to Create One.</td>
								</tr>
							';
						}
					?>
					<?php
						foreach($query as $data){
							$opening_days =json_decode($data->opening_days,true);?>
							<tr>
								<td><?php echo absint($data->id);?></td>
								<td><?php echo esc_html($data->store_name);?></td>
								<td><?php echo esc_html( $data->store_address);?></td>
								<td><?php echo esc_html(implode(',', $opening_days));?></td>
								<td><?php echo esc_html($data->pickup_time);?></td>
								<td>$<?php echo number_format($data->delivery_charge,2);?></td>
								<td>
									<button type="button" class="button button-small edit_store" data-id="<?php echo absint($data->id);?>">Edit</button>
									<button type="button" class="button button-small delete_store" data-id="<?php echo absint($data->id);?>">Delete</button>
								</td>
							</tr>
							<?php
						}
					?>
				</tbody>
			</table>
		</div>
		<div class="edp-store-modal" style="display:none;">
			<div class="edp-modal-content">
				<span class="edp-modal-close">&times;</span>
				<h2 id="modal-title">Add New Store</h2>
				<form id="edp-store-form">	<!----Form---->
					<input type="hidden" id="edpStore-id" name="store_id" value="0">
					<table class="form-table edp-form-table">
						<tr>
							<th><label for="store_name">Store Name <span class="required">*</span></label></th>
							<td><input type="text" id="store_name" name="store_name" class="regular-text" required></td>
						</tr>
						<tr>
							<th><label for="store_address">Store Address <span class="required">*</span></label></th>
							<td><textarea name="store_address" id="store_address" class="large-text" row="3" required></textarea></td>
						</tr>
						<tr>
							<th><label>Opening Days <span class="required">*</span></label></th>
							<td>
								<label><input type="checkbox" name="opening_days[]" value="Monday">Monday</label></br>
								<label><input type="checkbox" name="opening_days[]" value="Tuesday">Tuesday</label></br>
								<label><input type="checkbox" name="opening_days[]" value="Wednesday">Wednesday</label></br>
								<label><input type="checkbox" name="opening_days[]" value="Thursday">Thursday</label></br>
								<label><input type="checkbox" name="opening_days[]" value="Friday">Friday</label></br>
								<label><input type="checkbox" name="opening_days[]" value="Saturday">Saturday</label></br>
								<label><input type="checkbox" name="opening_days[]" value="Sunday">Sunday</label></br>
							</td>
						</tr>
						<tr>
							<th><label for="pickup_time">Pickup Time</label></th>
							<td>
								<input type="text" id="pickup_time" name="pickup_time" class="regular-text" placeholder="e.g., 10:00 AM - 6:00 PM">
							</td>
						</tr>
						<tr>
							<th><label for="delivery_charge">Delivery Charge</label></th>
							<td>
								<input type="number" id="delivery_charge" name="delivery_charge" class="small-text" step="0.01" min="0" readonly>$ <strong>Pro Feature</strong>
							</td>
						</tr>
					</table>
					<p class="submit_btn">
						<button type="submit" class="button button-primary" id="edpStore-save-btn"> Save Store</button>
						<button type="button" class="button button-primary edp-modal-closebtn"> Cancel</button>
					</p>
				</form>
			</div>
		</div>
		<div id="store_loader">
			<div id="loader_content">
				<div class="loader"></div>
				<div><h2 id="loader_text">Processing....</h2></div>
			</div>
		</div>
	</div>
</div>