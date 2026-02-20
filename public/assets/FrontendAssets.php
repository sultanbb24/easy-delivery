<?php
/**
 * Define NamSpace eeasyDeliveryPickup\public\assets
*/

namespace easyDeliveryPickup\public\assets;

defined('ABSPATH') || exit; //Prevent From Direct access

class FrontendAssets{
	/**
	 * Define Single Instance
	*/
	private static $instance = null;

	private function __construct(){
		add_action('wp_enqueue_scripts', [$this, 'load_frontend_assets']);
	}
	public static function instance(){
		if( is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function load_frontend_assets(){
		
		if(is_product() || is_checkout()){

			$general_settigns_options = get_option('edp_general_option');
			$date_range_restriction = isset($general_settigns_options['calender_max_selectable_day']) ? $general_settigns_options['calender_max_selectable_day'] : '';
			$disable_same_day = isset($general_settigns_options['disable_same_day']) && intval($general_settigns_options['disable_same_day'] === 1);
			$selected_theme = isset($general_settigns_options['edp_calender_theme_field']) ? $general_settigns_options['edp_calender_theme_field'] : 'default';
			wp_enqueue_style('edpCalender-css', EDP_URL . 'public/assets/css/edpCalender.min.css', array(), '1.0.0');
			if($selected_theme !== 'default'){
				wp_enqueue_style(
					'edp_theme',
					EDP_URL . "public/assets/css/{$selected_theme}.css",
					array('edpCalender-css'),
					'1.0.0'
				);
			}
			wp_enqueue_script( 
			'edpCalender_script', 
			EDP_URL . 'public/assets/js/edpcalender.js', 
			array(), 
			'1.0.0', 
			true
			);
			wp_enqueue_script( 
			'edp_front_script', 
			EDP_URL . 'public/assets/js/frontend.js', 
			array('jquery'), 
			'1.0.0', 
			true
			);
			add_filter('script_loader_tag', function($tag, $handle){
				if('edp_front_script' !== $handle){
					return $tag;
				}
				return str_replace('src', 'defer="defer" src', $tag);
			}, 10, 2);
			wp_localize_script(
				'edp_front_script',
				'edp_general_obj',
				array(
					'date_range' => $date_range_restriction,
					'disable_same_day' => $disable_same_day,
				)
			);
		}
		
		wp_enqueue_style( 
			'edp_front_css', 
			EDP_URL . 'public/assets/css/frontend.css', 
			array(), 
			'1.0.0', 
			'all' 
		);
	}
}