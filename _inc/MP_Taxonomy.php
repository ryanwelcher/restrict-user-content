<?php
/**
 * Class to create a new custom post type
 * 
 * Use this class in conjuction with the MP_Taxonomy to create new custom post types
 *
 */
class MP_Taxonomy {
	
	/**
	 * @var taxonomy name
	 */
	private $_name;
	
	/**
	 * @var post type 
	 */
	private $_cpt;
	
	private $_default_settings;
	
	private $_labels = array();
	
	private $_args = array();
	
	
	/**
	 * Constructor
	 *
	 * @param string Name of the custom post type
	 * @param array This array is basically a short hand to create the labels and args for the custom post type
	 */
	function __construct( $tax_name, $post_type, $settings_array ) {
		
		//get the name of the taxomy
		$this->_name = $tax_name;
		
		//get the post this is associated with
		$this->_cpt = $post_type;
		
		//define the default settings
		$this->_default_settings = array(
			
			'single_name' => 'Single',
			'plural_name' => 'Plural',
			'menu_name'	=> false,
			'tax_args' => array()
		);
		
		//parse args
		$passed_args = wp_parse_args( $settings_array, $this->_default_settings);
		extract($passed_args);
		
		
		//generate the labels
		$this->_labels = $this->generateLabels($single_name, $plural_name, $menu_name);
		
		//generate the args
		$this->_args = $this->createArgs( $tax_args );
		
		//register the tax
		register_taxonomy( $this->_name, $this->_cpt, $this->_args );
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
			'name'                       => sprintf( __( '%s', $this->_name . ' general name' , MP_TEXT_DOMAIN ), $name  ), //_x( 'Writers', 'taxonomy general name' ),
			'singular_name'              => sprintf( __( '%s', $this->_name . ' single name' , MP_TEXT_DOMAIN ), $singular ), //_x( 'Writer', 'taxonomy singular name' ),
			'search_items'               => sprintf( __( 'Search %s', MP_TEXT_DOMAIN ), $plural ), //__( 'Search Writers' ),
			'popular_items'              => sprintf( __( 'Popular %s', MP_TEXT_DOMAIN ), $plural ),//__( 'Popular Writers' ),
			'all_items'                  => sprintf( __( 'All %s', MP_TEXT_DOMAIN ), $plural ), //__( 'All Writers' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => sprintf( __( 'Edit %s', MP_TEXT_DOMAIN ), $singular ),// __( 'Edit Writer' ),
			'update_item'                => sprintf( __( 'Update %s', MP_TEXT_DOMAIN ), $singular ), //__( 'Update Writer' ),
			'add_new_item'               => sprintf( __( 'Add New %s', MP_TEXT_DOMAIN ), $singular ), //__( 'Add New Writer' ),
			'new_item_name'              => sprintf( __( 'New %s Name', MP_TEXT_DOMAIN ), $singular ),//__( 'New Writer Name' ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', MP_TEXT_DOMAIN ), $plural), //__( 'Separate writers with commas' ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', MP_TEXT_DOMAIN ), $plural), //__( 'Add or remove writers' ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', MP_TEXT_DOMAIN ), $plural), //__( 'Choose from the most used writers' ),
			'not_found'                  => sprintf( __( 'No %s found', MP_TEXT_DOMAIN ), $plural ),//__( 'No writers found.' ),
			'menu_name'                  => sprintf( __( '%s', MP_TEXT_DOMAIN ), $name )//__( 'Writers' ),
			
		);
		
		return $labels;
	 }
	 
	 
	 /**
	  * Create the arguments for the custom post type
	  * @param array Args to override the wp defaults - techincaly this will override all of the args if required. 
	  * 				  Refer to the defaults array for some preset items that we almost always need
	  */
	 private function createArgs( $tax_args = array() ) {
		 
		 $defaults = array(
		 	'labels'		=> $this->_labels, //use the generated labels
		 );
		 
		 $args = wp_parse_args( $tax_args, $defaults );
		 
		 return $args;
	 }
}