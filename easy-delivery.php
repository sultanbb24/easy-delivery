<?php
/**
 * Plugin Name: Easy Delivery - Delivery & Pickup Option for Woocommerce
 * Plugin URI: https://wordpress.org/plugins/easy-delivery/
 * Description: Easy Delivery- Provide delivery & Pickup Options for woocommerce Users to extend their business facility.
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Sultan Mahmud
 * Author URI: https://profiles.wordpress.org/sultanb24/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: easy-delivery
 * Requires Plugins: woocommerce
*/

defined('ABSPATH') || exit; //Prevent From Direct access

if( ! defined('EDP_URL')){
	define('EDP_URL', plugin_dir_url(__FILE__));
}
if( ! defined('EDP_DIR_PATH')){
	define('EDP_DIR_PATH', plugin_dir_path(__FILE__));
}
/**
 * Load Autoloader
*/
if( file_exists(__DIR__ . '/vendor/autoload.php')){

	require_once __DIR__ . '/vendor/autoload.php';
}
register_activation_hook(__FILE__, array(easyDeliveryPickup\includes\Installer::instance(), 'EDP_ACTIVATION'));

/**
 * Instance Main Class
*/
if( ! class_exists('SrEdpPlugin')):
	/**
	 * Easy Delivery Core Class 
	 */ 
	class SrEdpPlugin{
		/**
		 * The Single instance of the class
		*/
		protected static $_instance = null;

		/**
		 * Constructor
		*/
		protected function __construct(){
			$this->init();
		}
		/**
		 * Main Easy Delivery Instance
		*/
		public static function instance(){
			if( is_null(self::$_instance)){
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		/**
		 * Function For getting everything set up and Ready to run
		*/
		private function init(){
			
			/**
			 * Register Activation & Deactivation
			*/
			
			easyDeliveryPickup\includes\Controller::instance();
		}
	}
endif;

/**
 * Function for delaying initialization of the extension until after Woocommerce is Loaded
*/
if( ! function_exists('SrEdpPlugin_initialize')){
	function SrEdpPlugin_initialize(){
		if( ! class_exists('WooCommerce')){
			return;
		}
		$GLOBALS['SrEdpPlugin'] = SrEdpPlugin::instance();
	}
}
add_action('plugins_loaded', 'SrEdpPlugin_initialize', 10);
