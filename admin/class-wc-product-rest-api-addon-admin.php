<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/Orinwebsolutions
 * @since      1.0.0
 *
 * @package    Wc_Product_Rest_Api_Addon
 * @subpackage Wc_Product_Rest_Api_Addon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Product_Rest_Api_Addon
 * @subpackage Wc_Product_Rest_Api_Addon/admin
 * @author     Amila <amilapriyankara16@gmail.com>
 */
class Wc_Product_Rest_Api_Addon_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Product_Rest_Api_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Product_Rest_Api_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-product-rest-api-addon-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Product_Rest_Api_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Product_Rest_Api_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-product-rest-api-addon-admin.js', array( 'jquery' ), $this->version, false );

	}


	// filter the product response here
	function wc_api_add_custom_data_to_product( $response, $post, $request ) {
		// in this case we want to display the short description, so we copy it over to the description, which shows up in the app
		$tm_custom_data = [];
		// $acceptable_elements = ['textfield', 'textarea', 'selectbox', 'radiobuttons', 'range', 'checkboxes'];
		// $acceptable_elements = ['textfield', 'textarea', 'selectbox', 'radiobuttons', 'checkboxes'];
		$acceptable_elements = [ 
			'textfield'=>'textfield', 
			'textarea'=>'textarea', 
			'selectbox'=>'select', 
			'radiobuttons'=>'radio', 
			'checkboxes'=>'checkbox'];
		$filp_acceptable_elements = array_flip($acceptable_elements);
		$tm_meta = get_post_meta($post->get_id(), 'tm_meta', true);
		if($tm_meta){

			$element_types = $tm_meta['tmfbuilder']['element_type'];

			for ($i=0; $i <= (count($element_types)-1); $i++) {

				$tempArray = [];
				$field_keys = [];
				$field_values = [];

				foreach ($tm_meta['tmfbuilder'] as $tm_metakey => $tm_metavalue) {

					$element_type = $element_types[$i];
					if(!in_array($element_type, $filp_acceptable_elements)){//This is ignore other html elements
						break;
					}

					$compareArr = [
						// "{$element_type}_header_title",
						// "{$element_type}_min",
						// "{$element_type}_max",
						"multiple_{$element_type}_options_value",
						// "multiple_{$element_type}_options_price",
						// "multiple_{$element_type}_options_sale_price",
						// "multiple_{$element_type}_options_title"
						"{$element_type}_uniqid",
						"{$element_type}_internal_name"
					];

					if(!in_array($tm_metakey, $compareArr)){
						continue;
					}
					
					switch ($tm_metakey) {
						// case "{$element_type}_header_title":
						// 	$tempArray['_title'] = $tm_metavalue;
						// 	break;	
						case "{$element_type}_uniqid":
							$tempArray['_uniqid'] = $tm_metavalue;
							// var_dump($tempArray['_uniqid']);
							// echo '<br/>';
							// var_dump($tm_metavalue);
							break;																			
						case "multiple_{$element_type}_options_value":
							
							if($element_type == 'checkboxes'){
								$fieldkey = $acceptable_elements["{$element_type}"];
								for ($x=0; $x < count($tm_metavalue[0]) ; $x++) {
									array_push($field_keys,"tmcp_{$fieldkey}_{$i}_{$x}");
									array_push($field_values,"{$tm_metavalue[0][$x]}_{$x}");
								}
								$tempArray['_keys'] = $field_keys;
								$tempArray['_options_value'] = $field_values;
							}else{
								for ($x=0; $x < count($tm_metavalue[0]) ; $x++) {
									array_push($field_values,"{$tm_metavalue[0][$x]}_{$x}");
									$tempArray['_options_value'] = $field_values;
								}
							}

							break;	
						case "{$element_type}_internal_name":

							if($element_type != 'checkboxes'){
								$fieldkey = $acceptable_elements["{$element_type}"];
								if(!empty($tm_custom_data[$element_type]['_keys'])){
									if(is_array($tm_custom_data[$element_type]['_keys'])){//Verify if array exists
										$tempArray['_keys'] = $tm_custom_data[$element_type]['_keys'];
										array_push($tempArray['_keys'],"tmcp_{$fieldkey}_{$i}");
									}else{
										$tempArray['_keys'] = [$tm_custom_data[$element_type]['_keys'],"tmcp_{$fieldkey}_{$i}"];
									}
								}else{
									$tempArray['_keys'] = "tmcp_{$fieldkey}_{$i}";
								}
							}

							break;
						// case "{$element_type}_min":
						// 	$tempArray['_range_min'] = $tm_metavalue;
						// 	break;
						// case "{$element_type}_max":
						// 	$tempArray['_range_max'] = $tm_metavalue;
						// 	break;															
						// case "multiple_{$element_type}_options_title":
						// 	$tempArray['_options_title'] = $tm_metavalue;
						// 	break;							
						// case "multiple_{$element_type}_options_value":
						// 	$tempArray['_options_value'] = $tm_metavalue;
						// 	break;
						// case "multiple_{$element_type}_options_price":
						// 	$tempArray['_options_price'] = $tm_metavalue;
						// 	break;
						// case "multiple_{$element_type}_options_sale_price":
						// 	$tempArray['_options_sale_price'] = $tm_metavalue;
						// 	break;
						default:
							break;
					}
				}

				if($tempArray){
					// $tm_custom_data[$element_type] = $tempArray;
					$id = $tempArray['_uniqid'][0];
					unset($tempArray['_uniqid']);
					$tm_custom_data[$id] = $tempArray;
				}
				
			}
			$newOptions = [];
			foreach ($response->data['options'] as $key => $optionsArry) {
				$options = [];
				if (array_key_exists($optionsArry['id'][0], $tm_custom_data)){
					$optionsArry['_keys'] = $tm_custom_data[$optionsArry['id'][0]]['_keys'];
					$optionsArry['_options_value'] = $tm_custom_data[$optionsArry['id'][0]]['_options_value'];
				}

				array_push($newOptions, $optionsArry);

			}
			$response->data['options']  = $newOptions;
			// $response->data['tm_product_custom_data'] = $tm_custom_data;
		}
		return $response;
  
  	}

	// function wc_api_add_custom_data_to_product_v1( $product_data, $product, $fields, $this_server ) {
	// 	// retrieve a custom field and add it to API response
	// 	$product_data['your_new_key_2'] = 'Radini please show this';
	// 	return $product_data;
	// }

}
