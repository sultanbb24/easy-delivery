<?php
/**
 * Define NamSpace easyDeliveryPickup\public
*/

namespace easyDeliveryPickup\public;

use easyDeliveryPickup\public\assets\FrontendAssets;

defined('ABSPATH') || exit;

class Frontend{

	private static $instance = null;

	private function __construct(){
		$this->initialize_wc_action();
	}
	public static function instance(){
		if( is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function initialize_wc_action(){
		FrontendAssets::instance();
		//Classic Checkout Action
		add_action('woocommerce_before_checkout_billing_form', [$this, 'render_delivery_pickup_fields_classic_checkout']);
		//Share for both Single product page & checkout page
		add_action('woocommerce_checkout_create_order_line_item', [$this, 'save_store_detail_to_order'], 10, 4);
		add_action('woocommerce_checkout_process', [$this, 'checkout_fields_validation']);
	}
	public function save_store_detail_to_order($item, $cart_item_key, $values, $order){
		//Single Product page Operation
		if(isset($values['edp-order-type'])){
			
			$item->add_meta_data(__('Order Type', 'easy-delivery'), $values['edp-order-type']);

			if($values['edp-order-type'] === 'pickup'){
				$item->add_meta_data(__('Nearest Store', 'easy-delivery'), $values['edp_pickup_store_name']);
				$item->add_meta_data(__('Store Address', 'easy-delivery'), $values['edp_pickup_store_address']);
				$item->add_meta_data(__('Pickup Date', 'easy-delivery'), $values['edp_pickup_date']);
				$item->add_meta_data(__('Pickup Time', 'easy-delivery'), $values['edp_pickup_time']);
			}else{
				$item->add_meta_data(__('Nearest Store', 'easy-delivery'), $values['edp_delivery_store_name']);
				$item->add_meta_data(__('Delivery Date', 'easy-delivery'), $values['edp_delivery_date']);
			}
		}
		//Classic Page Checkout Operation
		if(isset($_POST['woocommerce-process-checkout-nonce'])){
			if( ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['woocommerce-process-checkout-nonce'])), 'woocommerce-process_checkout')){
				return;
			}
		}
		if(isset($_POST['edp_checkout_order_type']) && ! empty($_POST['edp_checkout_order_type'])){
			$order_type = sanitize_text_field(wp_unslash($_POST['edp_checkout_order_type']));
			$item->add_meta_data(__('Order Type', 'easy-delivery'), $order_type);

			if($order_type === 'pickup'){
				if( ! empty($_POST['edp_selected_pickup_store_name'])){
					$item->add_meta_data(__('Pickup Store', 'easy-delivery'), sanitize_text_field(wp_unslash($_POST['edp_selected_pickup_store_name'])));
				}
				if( ! empty($_POST['edp_selected_pickup_store_address'])){
					$item->add_meta_data(__('Store Address', 'easy-delivery'), sanitize_text_field(wp_unslash($_POST['edp_selected_pickup_store_address'])));
				}
				if( ! empty($_POST['edp_checkout_pickup_date'])){
					$item->add_meta_data(__('Pickup Date', 'easy-delivery'), sanitize_text_field( wp_unslash($_POST['edp_checkout_pickup_date'])));
				}
				if( ! empty($_POST['edp_checkout_pickup_time'])){
					$item->add_meta_data(__('Pickup Time', 'easy-delivery'), sanitize_text_field(wp_unslash($_POST['edp_checkout_pickup_time'])));
				}
			}else{
				if( ! empty($_POST['edp_selected_delivery_store_name'])){
					$item->add_meta_data(__('Delivery Store', 'easy-delivery'), sanitize_text_field(wp_unslash($_POST['edp_selected_delivery_store_name'])));
				}
				if( ! empty($_POST['edp_checkout_delivery_date'])){
					$item->add_meta_data(__('Delivery Date', 'easy-delivery'), sanitize_text_field(wp_unslash($_POST['edp_checkout_delivery_date'])));
				}
			}
		}
	}
	//Checkout Field OPeration from here
	public function render_delivery_pickup_fields_classic_checkout(){
		global $wpdb;
		$table = "{$wpdb->prefix}edp_store_details";
		$stores = get_transient('edp_stores_cache');

		if(false === $stores){
			$stores = $wpdb->get_results("SELECT * FROM `". esc_sql($table) . "` ORDER BY id DESC");
			set_transient('edp_stores_cache', $stores, DAY_IN_SECONDS);
		}
		if(empty($stores)){
			return;
		}
		$general_option = get_option('edp_general_option', [] );
		$minimum_delivery_time = isset($general_option['delivery_time']) ? intval($general_option['delivery_time']) : 0;
		echo '<div class="edp-checkout-section">';
		// include __DIR__ .'/templates/classic-checkout-fields.php';
		$this->get_template('classic-checkout-fields.php', ['stores' => $stores]);
		echo '</div>';
	}
	public function get_template($template_name, $args = []){
		if($args && is_array($args)){
			extract($args);
		}
		$template = locate_template(array(
			'easy-delivery/'. $template_name
		));
		if(! $template){
			$template = EDP_DIR_PATH . 'public/templates/' . $template_name;
		}
		if(file_exists($template)){
			include $template;
		}
	}
	public function checkout_fields_validation(){
		if(isset($_POST['woocommerce-process-checkout-nonce'])){
			if( ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['woocommerce-process-checkout-nonce'])), 'woocommerce-process_checkout')){
				return;
			}
		}
		if ( empty( $_POST['edp_checkout_order_type'] ) ) {
		wc_add_notice( __( 'Please select an <strong>Order Type</strong> (Pickup or Delivery).', 'easy-delivery' ), 'error' );
		return; // No need to check other fields if type isn't selected
		}

		$order_type = sanitize_text_field( $_POST['edp_checkout_order_type'] );

		// 2. Validate based on selected type
		if ( 'pickup' === $order_type ) {
			if ( empty( $_POST['edp_checkout_pickup_store'] ) ) {
				wc_add_notice( __( 'Please select a <strong>Pickup Store</strong>.', 'easy-delivery' ), 'error' );
			}
			if ( empty( $_POST['edp_checkout_pickup_date'] ) ) {
				wc_add_notice( __( 'Please select a <strong>Pickup Date</strong>.', 'easy-delivery' ), 'error' );
			}
		} 
		elseif ( 'delivery' === $order_type ) {
			if ( empty( $_POST['edp_checkout_delivery_store'] ) ) {
				wc_add_notice( __( 'Please select a <strong>Delivery Store</strong>.', 'easy-delivery' ), 'error' );
			}
			if ( empty( $_POST['edp_checkout_delivery_date'] ) ) {
				wc_add_notice( __( 'Please select a <strong>Delivery Date</strong>.', 'easy-delivery' ), 'error' );
			}
		}
	}

	
}