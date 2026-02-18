<?php
/**
 * Define NamSpace easyDeliveryPickup\includes
*/
namespace easyDeliveryPickup\includes;

use easyDeliveryPickup\Admin\assets;
use easyDeliveryPickup\Admin\Admin;
use easyDeliveryPickup\public\Frontend;

defined('ABSPATH') || exit; //Prevent From Direct access

class Controller{
	/**
	 * Define Single Instance
	*/
	private static $instance = null;
	/**
	 * Define \easyDeliveryPickup\Admin
	*/

	private function __construct(){
		$this->register_initialize_settings();
	}
	public static function instance(){
		if( is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function register_initialize_settings(){

		//Run Assets

		assets::instance();

		if( is_admin()){
			Admin::instance();
		}
		if( ! is_admin()){
			Frontend::instance();
		}
		
	}
}
