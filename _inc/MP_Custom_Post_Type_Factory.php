<?php
/**
 * Factory Class to create Custom Post Types
 *
 * This class is the factory class to quickly create custom post types
 * @param string Name of the custom post type
 * @param array  Array of various settings. Refer to MP_Custom_Post_Type for details
 * @usage
 
 MP_Custom_Post_Type_Factory::create( 'post_name', array( 'single_name' => 'Post Name', 'plural_name' => 'Post Names', 'post_type_args' => array() ));
 
 
 
 
 */
class MP_Custom_Post_Type_Factory {
	
	public static function create( $cpt_name, $settings_array  ) {
		
		return new MP_Custom_Post_Type( $cpt_name, $settings_array );
	}
}