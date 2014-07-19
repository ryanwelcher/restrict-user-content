<?php

/**
 * Interface for MP_Plugin_Base
 */

if(!interface_exists('I_MP_Plugin_Base' )):

interface I_MP_Plugin_Base {
	
	/**
	 * The installation hook callback
	 */
	function mp_plugin_install();
	
	/**
	 * Create the settings page meta boxes
	 */
	function mp_plugin_create_meta_boxes();
	
	/**
	 * Settings save method
	 */
	function mp_plugin_save_settings();
}

endif;