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
		$acceptable_elements = ['textfield', 'textarea', 'selectbox', 'radiobuttons', 'range'];
		$tm_meta = get_post_meta($post->get_id(), 'tm_meta', true);
		if($tm_meta){
			$element_types = $tm_meta['tmfbuilder']['element_type'];
			for ($i=0; $i <= (count($element_types)-1); $i++) { 
				$tempArray = [];
				foreach ($tm_meta['tmfbuilder'] as $tm_metakey => $tm_metavalue) {
					$element_type = $element_types[$i];
					if(!in_array($element_type, $acceptable_elements)){//This is ignore other html elements
						break;
					}

					$compareArr = [
						"{$element_type}_header_title",
						"{$element_type}_min",
						"{$element_type}_max",
						"multiple_{$element_type}_options_value",
						"multiple_{$element_type}_options_price",
						"multiple_{$element_type}_options_sale_price"
					];

					if(!in_array($tm_metakey, $compareArr)){
						continue;
					}
					// error_log($element_type);
					switch ($tm_metakey) {
						case "{$element_type}_header_title":
							$tempArray['_title'] = $tm_metavalue;
							break;
						case "{$element_type}_min":
							$tempArray['_range_min'] = $tm_metavalue;
							break;
						case "{$element_type}_max":
							$tempArray['_range_max'] = $tm_metavalue;
							break;															
						case "multiple_{$element_type}_options_value":
							$tempArray['_options_value'] = $tm_metavalue;
							break;
						case "multiple_{$element_type}_options_price":
							$tempArray['_options_price'] = $tm_metavalue;
							break;
						case "multiple_{$element_type}_options_sale_price":
							$tempArray['_options_sale_price'] = $tm_metavalue;
							break;
						default:
							break;
					}
				}
				if($tempArray){
					$tm_custom_data[$element_type] = $tempArray;
				}
				
			}
			// error_log(print_r($tm_custom_data, true));
			// error_log(print_r($post, true));
			$response->data['tm_product_custom_data'] = $tm_custom_data;
		}
		return $response;
  
  	}

	// function wc_api_add_custom_data_to_product_v1( $product_data, $product, $fields, $this_server ) {
	// 	// retrieve a custom field and add it to API response
	// 	$product_data['your_new_key_2'] = 'Radini please show this';
	// 	return $product_data;
	// }

}
