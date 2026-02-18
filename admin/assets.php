<?php
/**
 * Define Namspace
*/
namespace easyDeliveryPickup\Admin;

use easyDeliveryPickup\includes\AjaxHandler;

defined('ABSPATH') || exit; //Prevent From Direct access

class assets{
	/**
	 * Define Single instance
	*/
	private static $instance = null;

	private function __construct(){
		add_action('admin_enqueue_scripts', [$this, 'load_assets']);
		if(defined('DOING_AJAX') && DOING_AJAX ){
			AjaxHandler::instance();
		}
	}
	public static function instance(){
		if( is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function load_assets(){
		wp_enqueue_script( 
			'edp-admin-script', 
			EDP_URL . 'admin/assets/js/admin.js', 
			array('jquery'), 
			'1.0.0', 
			array(
				'in_footer' => true,
			)
		);
		/**
		 * Localize Script For Ajax Handelar
		*/
		wp_localize_script(
			'edp-admin-script',
			'edp_obj',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('edp_store_nonce')
			)
		);

		//Load Stylesheet
		wp_enqueue_style( 
			'edp-admin-stylesheet', 
			EDP_URL . 'admin/assets/css/admin.css', 
			array(), 
			'1.0.0', 
			'all' 
		);
	}
}