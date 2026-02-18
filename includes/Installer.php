<?php
/**
 * Define NamSpace easyDeliveryPickup\includes
*/

namespace easyDeliveryPickup\includes;

defined('ABSPATH') || exit; //Prevent From Direct access

class Installer{

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
	
	public function EDP_ACTIVATION(){
		$this->create_table();
	}
	public function create_table(){
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = "{$wpdb->prefix}edp_store_details";
		$sql = "CREATE TABLE IF NOT EXISTS $table_name(
			id int(11) unsigned NOT NULL AUTO_INCREMENT,
			store_name varchar(255) NOT NULL,
			store_address text NOT NULL,
			opening_days text NOT NULL,
			pickup_time varchar(100) NOT NULL,
			delivery_charge decimal(10,2) NOT NULL,
			created_by bigint(20) unsigned NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
