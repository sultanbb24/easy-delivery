<?php

/**
 * Define Namespace
*/
namespace easyDeliveryPickup\includes;

defined('ABSPATH') || exit; //Prevent From Direct access

class AjaxHandler{

	//Define Single Instance

	private static $instance = null;

	private function __construct(){
		add_action('wp_ajax_save_edp_store_data', [$this, 'save_store_details']);
		add_action( 'wp_ajax_edit_store_details', [$this, 'edit_store_details']);
		add_action('wp_ajax_delete_store_details', [$this, 'delete_store_details']);
	}
	public static function instance(){
		if( is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function save_store_details(){
		//Nonce Check
		check_ajax_referer('edp_store_nonce', 'nonce');
		//Permission Check
		if( ! current_user_can('manage_options')){
			wp_send_json_error('Unauthorized');
		}
		$data = Data::instance();
		$form_data = isset($_POST['data']) ? wp_unslash($_POST['data']) : [] ; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		//Convert Serialize Array to Associative Array
		$param = [];
		foreach($form_data as $item){
			if(strpos($item['name'], '[]') !== false){
				$param[str_replace('[]', '', $item['name'])][] = $item['value'];
			}else{
				$param[$item['name']] = $item['value'];
			}
		}
		$store_id = isset($param['store_id']) ? intval($param['store_id']) : 0;
		$result = $data->insert_edp_store_details($param);
		if(is_wp_error($result)){
			wp_send_json_error($result->get_error_message());
		}
		wp_cache_delete('edp_store_list_admin');
		delete_transient('edp_stores_cache');
		wp_send_json_success([
			'message' => $store_id > 0 ? 'Store Update Successfully' : 'Store Saved Successfully',
		]);
	}
	// Edit Store
	public function edit_store_details(){
		check_ajax_referer('edp_store_nonce', 'security');
		if( ! current_user_can('manage_options')){
			wp_send_json_error('Unauthorized');
		}
		global $wpdb;
		$table_name = "{$wpdb->prefix}edp_store_details";
		$store_id = isset($_POST['store_id']) ? sanitize_text_field(intval($_POST['store_id'])) : 0;
		$cache_key = 'edp_store_' . $store_id;
		$store = wp_cache_get($cache_key);
		if(false === $store){
			$store = $wpdb->get_row($wpdb->prepare("SELECT * FROM `" . esc_sql($table_name) . "` WHERE id = %d",  $store_id), ARRAY_A); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			wp_cache_set($cache_key, $store);
		}
		if($store){
			$store['opening_days'] = json_decode($store['opening_days'], true);
			wp_send_json_success($store);
		}else{
			wp_send_json_error('Store Not Found');
		}
	}
	//Delete Store Details
	public function delete_store_details(){
		check_ajax_referer( 'edp_store_nonce', 'nonce');
		if( ! current_user_can('manage_options')){
			wp_send_json_error('Unauthorized');
		}
		global $wpdb;
		$table_name = "{$wpdb->prefix}edp_store_details";
		$store_id = isset($_POST['store_id']) ? sanitize_text_field(intval($_POST['store_id'])) : 0;
		$result = $wpdb->delete($table_name, array('id' => $store_id)); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		wp_cache_delete('edp_store_' . $store_id);
		wp_cache_delete('edp_store_list_admin');
		delete_transient('edp_stores_cache');
		if($result){
			wp_send_json_success('Store Deleted Successfully! Refreshing');
		}else{
			wp_send_json_error('Failed to delete Store');
		}
	}
}