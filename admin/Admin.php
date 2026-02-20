<?php
/**
 * Define NamSpace easyDeliveryPickup\Admin
*/
namespace easyDeliveryPickup\Admin;

defined('ABSPATH') || exit; //Prevent from direct access

class Admin{
	
	private static $instance = null;

	/**
	 * Define Tab
	*/
	private $tab = [];

	private $option_group = 'edp-settings';

	private function __construct(){
		$this->register_initialize_action();
	}
	public static function instance(){
		if( is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function register_initialize_action(){
		add_action('admin_menu', [$this, 'add_admin_menu']);
		add_action('admin_init', [$this, 'render_register_setting']);
	}
	public function add_admin_menu(){
		add_menu_page(
			__('Easy Delivery', 'easy-delivery'), 
			__('Easy Delivery', 'easy-delivery'), 
			'manage_options', 
			'easy-delivery', 
			[$this, 'render_menu_content'], 
			'dashicons-store', 
			55 
		);
	}
	/**
	 * Settings Api Callback
	 * @return Settings Tab Content
	*/
	public function render_register_setting(){
		/**
		 * Render Each tab content Seperately with this method
		*/
		$this->render_general_tab_content();
	}
	private function render_general_tab_content(){
		register_setting($this->option_group, 'edp_general_option',['sanitize_callback' => [$this, 'sanitize_general_option']]);
		add_settings_section( 
			'general_section', 
			__('General Settings', 'easy-delivery'), 
			[$this, 'render_general_settings_section'], 
			'easy_delivery_general', //$Page Section Page Slug
		);
		add_settings_field(
			'edp_calender_theme_field', 
			__('Calender Theme', 'easy-delivery'), 
			[$this, 'render_settings_fields_select'], 
			'easy_delivery_general', 
			'general_section', 
			array(
				'label_for' => 'edp_calender_theme_field',
				'option_name' => 'edp_general_option',
				'field_name' => 'edp_calender_theme_field',
				'tip' => 'Choose e visual color for Your Calender',
				'options' => array(
					'default' => 'Default Light',
					'dark' =>__('Dark Mode', 'easy-delivery') ,
					'material_blue' =>__('Material Blue', 'easy-delivery') ,
					'material_green' =>__('Material Green', 'easy-delivery') ,
					'material_red' =>__('Material Red', 'easy-delivery') ,
					'confetti' =>__('Confetti (Colorful)', 'easy-delivery') ,
				),
			),
		);
		add_settings_field( 
			'disable_same_day', 
			__('Disable Same Day', 'easy-delivery'), 
			[$this, 'render_settings_field_checkbox'], 
			'easy_delivery_general', 
			'general_section', 
			array(
				'label_for' => 'disable_same_day',
				'option_name' => 'edp_general_option',
				'field_name' => 'disable_same_day',
				'label' => __('Disable Same Day Delivery', 'easy-delivery'),
				'tip' => __('Check this box to disable Same Day Delivery / Pickup', 'easy-delivery'),
			)
		);
		add_settings_field(
			'calender_max_selectable_day', 
			__('Date Range Restriction (Days)', 'easy-delivery'), 
			[$this, 'render_settings_field_number'], 
			'easy_delivery_general', 
			'general_section', 
			array(
				'label_for' => 'calender_max_selectable_day',
				'option_name' => 'edp_general_option',
				'field_name' => 'calender_max_selectable_day',
				'tip' => ' Enter Date Range Restriction.So that user can select date from current day to your Max Day. Default 30 Days',
			),
		);
		add_settings_field(
			'delivery_time', 
			__('Minimum Delivery/Pickup Time (in hours)', 'easy-delivery'), 
			[$this, 'render_settings_field_number'], 
			'easy_delivery_general', 
			'general_section', 
			array(
				'label_for' => 'delivery_time',
				'option_name' => 'edp_general_option',
				'field_name' => 'delivery_time',
				'tip' => ' Enter Your Process time for Delivery or Pickup. Leave blank If you do not have Minimum Process time',
				'feature' => 'Pro Feature',
			),
		);
		add_settings_field(
			'max_delivery', 
			'Max Orders Per day/slot', 
			[$this, 'render_settings_field_number'], 
			'easy_delivery_general', 
			'general_section', 
			array(
				'label_for'=>'max_delivery',
				'option_name' => 'edp_general_option',
				'field_name' => 'max_delivery',
				'tip' => ' Add your Ability to deliver Maximum Order In a Day.',
				'feature' => 'Pro Featture'
			), 
		);
	}
	public function render_general_settings_section(){
		echo"<p>Configure General Settings</p>";
	}
	public function render_settings_field_number($args){
		$option = get_option($args['option_name']);
		$value = isset($option[$args['field_name']]) ? $option[$args['field_name']] : '';
		$tip = isset($args['tip']) ? $args['tip'] : '';
		$feature =isset($args['feature']) ? $args['feature'] : '';
		$readonly = ! empty($feature) ? 'readonly' : ''; 
		printf(
			'<input type="number" class="small-text" id="%s" name="%s[%s]" value="%s" min="0" step="1" %s>',
			esc_attr($args['label_for']),
			esc_attr($args['option_name']),
			esc_attr($args['field_name']),
			esc_attr($value),
			esc_attr($readonly),
		);
		if( !empty($tip || $feature)){
			echo "<span>".esc_html($tip)."<strong> ".esc_html($feature)."</strong>"."</span>";
		}
	}
	public function render_settings_fields_select($args){
		$options = get_option($args['option_name']);
		$field_name = $args['field_name'];
		$value = isset($options[$field_name]) ? $options[$field_name] : '';
		$select_options = isset($args['options']) ? $args['options'] : array();
		$tip = isset($args['tip']) ? $args['tip'] : '';
		?>
		<select name="<?php echo esc_attr($args['option_name'] . '[' . $field_name . ']');?>" id="<?php echo esc_attr($field_name);?>" class="regular-text" aria-describedby="<?php echo esc_attr($field_name. '-description');?>">
			<?php foreach($select_options as $value_key => $label) : ?>
			<option value="<?php echo esc_attr($value_key);?>" <?php selected($value, $value_key);?>><?php echo esc_html($label);?></option>
			<?php endforeach;?>
		</select>
		<?php
		if( ! empty($tip)){
			echo "<span>".esc_html($tip)."</span>";
		}
	}
	public function render_settings_field_checkbox($args){
		$options = get_option($args['option_name']);
		$field_name = $args['field_name'];
		$checked = isset($options[$field_name]) ? $options[$field_name] : '0';
		?>
		<fieldset>
			<label for="<?php echo esc_attr($field_name);?>">
				<input type="checkbox" id="<?php echo esc_attr($field_name);?>" name="<?php echo esc_attr($args['option_name'] . '[' . $field_name . ']');?>" value="1" <?php checked(1, $checked, true);?> aria-describedby="<?php echo esc_attr($field_name . '-description');?>" />
				<?php echo esc_html($args['label']);?>
			</label>
		</fieldset>
		<?php
	}
	public function render_menu_content(){
		//Check User Capability
		if( ! current_user_can( 'manage_options')){
			return;
		}
		$this->tab = [
			'general' => __('General', 'easy-delivery'),
			'store' => __('Store Setting', 'easy-delivery'),
		];
		include __DIR__ . '/templates/easy-delivery-admin.php';
	}
	public function sanitize_general_option($input){
		$sanitized = [];
		if(isset($input['edp_calender_theme_field'])){
			$sanitized['edp_calender_theme_field'] = sanitize_text_field($input['edp_calender_theme_field']);
		}
		if(isset($input['disable_same_day'])){
			$sanitized['disable_same_day'] = isset($input['disable_same_day']) ? absint($input['disable_same_day']) : 0;
		}
		if(isset($input['calender_max_selectable_day']) && $input['calender_max_selectable_day'] !==''){
			$sanitized['calender_max_selectable_day'] = absint($input['calender_max_selectable_day']);
		}
		if(isset($input['delivery_time']) && $input['delivery_time'] !==''){
			$sanitized['delivery_time'] = absint($input['delivery_time']);
		}
		if(isset($input['max_delivery']) && $input['max_delivery'] !==''){
			$sanitized['max_delivery'] = absint($input['max_delivery']);
		}
		return $sanitized;
	}
}