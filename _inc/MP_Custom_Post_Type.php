<?php
/**
 * Class to create a new custom post type
 * 
 * Use this class in conjuction with the MP_Custom_Post_Type_Factory to create new custom post types
 *
 */
class MP_Custom_Post_Type {
	
	/**
	 * @var post name
	 */
	private $_name;
	
	private $_default_settings;
	
	private $_labels = array();
	
	private $_args = array();
	
	
	/**
	 * Constructor
	 *
	 * @param string Name of the custom post type
	 * @param array This array is basically a short hand to create the labels and args for the custom post type
	 */
	function __construct( $cpt_name, $settings_array ) {
		
		//get the name of the custom post type
		$this->_name = $cpt_name;
		
		//define the default settings
		$this->_default_settings = array(
			
			'single_name' => 'Single',
			'plural_name' => 'Plural',
			'menu_name'	=> false,
			'post_type_args' => array()
		);
		
		//parse args
		$passed_args = wp_parse_args( $settings_array, $this->_default_settings);
		extract($passed_args);
		
		
		//generate the labels
		$this->_labels = $this->generateLabels($single_name, $plural_name, $menu_name);
		
		//generate the args
		$this->_args = $this->createArgs( $post_type_args );
		
		//register the post type
		register_post_type( $this->_name, $this->_args );
	}
	
	
	/**
	 * Method to generate and return the labels
	 * @param string The singular label name for the custom post type
	 * @param string The plural label for the custom post type
	 * @param string | bool The string for the menu_name/name class
	 */
	 private function generateLabels($singular, $plural, $menu_name = false) {
		 
		$name = ($menu_name) ? $menu_name : $plural;
	 	
		$labels = array(
			'name'					=> sprintf( __( '%s', MP_TEXT_DOMAIN ), $name  ),
			'singular_name' 		=> sprintf( __( '%s', MP_TEXT_DOMAIN ), $singular  ),
			'add_new' 				=> sprintf( _x( 'Add %s',$this->_name, MP_TEXT_DOMAIN ), $singular  ),
			'add_new_item' 		=> sprintf( __( 'Add New %s', MP_TEXT_DOMAIN ), $singular ),
			'edit_item' 			=> sprintf( __( 'Edit %s', MP_TEXT_DOMAIN ), $singular ),
			'new_item' 				=> sprintf( __( 'New %s' , MP_TEXT_DOMAIN ), $singular ),
			'view_item' 			=> sprintf( __( 'View %s' , MP_TEXT_DOMAIN ), $singular),
			'search_items' 		=> sprintf( __( 'Search %s', MP_TEXT_DOMAIN ), $plural ),
			'not_found' 			=> sprintf( __( 'No %s found', MP_TEXT_DOMAIN ), $plural),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash', MP_TEXT_DOMAIN ), $plural),
			'parent_item'		 	=> sprintf( __( '%s', MP_TEXT_DOMAIN ), $singular  ),
			'parent_item_colon' 	=> sprintf( __( '%s:', MP_TEXT_DOMAIN ), $singular  ),
			'menu_name' 			=> sprintf( __( '%s', MP_TEXT_DOMAIN ), $name ),
		);
		
		return $labels;
	 }
	 
	 
	 /**
	  * Create the arguments for the custom post type
	  * @param array Args to override the wp defaults - techincaly this will override all of the args if required. 
	  * 				  Refer to the defaults array for some preset items that we almost always need
	  */
	 private function createArgs( $post_type_args = array() ) {
		 
		 $defaults = array(
		 	'labels'		=> $this->_labels, //use the generated labels
    		'public'    => true,
    		'show_ui'	=> true,
    		'supports'	=> array( 'title', 'editor','thumbnail','custom_fields' ),
			'register_meta_box_cb' => "{$this->_name}_meta_callback"
		 );
		 
		 $args = wp_parse_args( $post_type_args, $defaults );
		 
		 return $args;
	 }
}