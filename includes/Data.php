<?php
/**
 * Define NameSpace easyDeliveryPickup\includes
*/
namespace easyDeliveryPickup\includes;

defined('ABSPATH') || exit; //Prevent From Direct access

class Data{

	/**
		* The Single instance of the class
	*/

	private static $instance = null;

	/**
		* Constructor
	*/
	private function __construct(){}

	/**
		* Data Instance
	*/
	public static function instance(){
		if( is_null(self::$instance)){

			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Data Pass From AjaxHandler
	*/
	public function insert_edp_store_details( $args = [] ){
		global $wpdb;
		$table ="{$wpdb->prefix}edp_store_details";
		$store_id = isset($args['store_id']) ? intval($args['store_id']) : 0;
		$data = [
			'store_name' => sanitize_text_field($args['store_name']),
			'store_address' => sanitize_textarea_field($args['store_address']),
			'opening_days' =>json_encode(isset($args['opening_days']) ? array_map('sanitize_text_field', $args['opening_days']) : array()),
			'pickup_time' => sanitize_text_field($args['pickup_time']),
			'delivery_charge' =>floatval($args['delivery_charge']),
		];
		if($store_id > 0){
			$updated = $wpdb->update( //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$table, 
			$data, 
			['id' =>$store_id],
			['%s', '%s', '%s', '%s', '%f', '%d'], );
			if( false === $updated){
				return new \WP_Error('failed-to-updated', 'Failed to Update Data');
			}
			wp_cache_delete('edp_store_'. $store_id);
			wp_cache_delete('edp_store_list_admin');
			delete_transient('edp_store_cache');
			return $store_id;
		}else{
			$data['created_by'] = get_current_user_id();
			$data['created_at'] = current_time('mysql');
			$inserted = $wpdb->insert( //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$table, 
			$data, 
			['%s', '%s', '%s', '%s', '%f', '%d', '%s'],);
		
			if( ! $inserted){
				return new \WP_Error('failed-to-insert', 'Failed to insert Data');
			}
			wp_cache_delete('edp_store_list_admin');
			delete_transient('edp_store_cache');
			return $wpdb->insert_id;
		}
	}
}