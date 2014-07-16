<?php
/**
 * Factory Class to create Taxonomnies
 *
 * This class is the factory class to quickly create custom post types
 * @param string Name of the custom post type
 * @param array  Array of various settings. Refer to MP_Custom_Post_Type for details
 * @usage
 
 MP_Taxonomy_Factory::create( 'tax_name', 'cpt_name', array( 'single_name' => 'Post Name', 'plural_name' => 'Post Names', 'tax_args' => array() ));
 
 */
 
class MP_Taxonomy_Factory {
	
	public static function create( $tax_name, $cpt_name, $settings_array  ) {
		
		return new MP_Taxonomy( $tax_name, $cpt_name, $settings_array );
	}
}