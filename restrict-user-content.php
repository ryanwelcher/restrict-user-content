<?php
/**
 * Plugin Name: Restrict User Content
 * Description: Limits the Posts/Media pages to only show content created by the logged in user. 
 * Author: Ryan Welcher
 * Version: 1.0
 * Author URI: http://www.mediaplusadvertising.com
 * Text Domain: ruc
 */


if( !class_exists('Restrict_User_Content') ) :

//get the base class
if(!class_exists('MP_Plugin_Base')) {
	require_once plugin_dir_path( __FILE__ ) . '/_inc/MP_Plugin_Base.php';
}
//get the interface
if( !interface_exists('I_MP_Plugin_Base') ) {
	require_once plugin_dir_path( __FILE__ ) . '/_inc/I_MP_Plugin_Base.php';
}



/**
 * Class Definition
 */
class Restrict_User_Content extends MP_Plugin_Base implements I_MP_Plugin_Base {
	
	/**
	 * @var bool Does this plugin need a settings page?
	 */
	private $_has_settings_page = true;
	
	/**
	 * var string Slug name for the settings page
	 */
	private $_settings_page_name = 'restrict_user_content_settings_page';
	
	
	/**
	 * @var array default settings
	 */
	private $_default_settings = array();
	
	
	/**
	 * @var The name of the settings in the database
	 */
	private $_settings_name = 'restrict_user_content_settings';
	
	
	/**
	 * Construct
	 */
	function __construct() {
		
		//call super class constructor
		parent::__construct( __FILE__, $this->_has_settings_page, $this->_settings_page_name );
		
		//set some details
		$this->_settings_menu_title = 'Restrict User Content';
		
		//Start your custom goodness
		add_action('pre_get_posts', 				array( $this, 'mp_pre_get_posts_media_user_only') );
		add_filter('parse_query',					array( $this, 'ruc_parse_query_useronly'		) );
		add_filter('ajax_query_attachments_args',	array( $this, 'ruc_ajax_attachments_useronly'		) );




	}


	//=================
	// ACTION CALLBACKS
	//=================

	/**
	 * Augment the query on the media page
	 * 
	 * This is tied into the settings to show media uploaded by the user and 
	 * any others as indicated in the settings panel. This will allow site admins to create
	 * a sandbox with images that are available to all users.
	 */
	function mp_pre_get_posts_media_user_only( $query ) {

		if(strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/upload.php' ) !== false ) {
			if( is_main_query() ) {
				
				if ( !current_user_can( 'update_core' ) ) {
	            	global $current_user;
	            	//var_dump( get_users( array( 'role' => 'subscriber', 'fields' => 'display_name') ) );
	            	$query->set( 'author__in', array( $current_user->id, '1' ) );
	            	//var_dump( $query );
	        	}
			}
		}
	}

	
	//=================
	// FILTER CALLBACKS
	//=================


	/**
	 * Only show the posts for the current non-admin user.
	 * 
	 * Great function written by Sarah Gooding.
	 * @link {http://premium.wpmudev.org/blog/how-to-limit-the-wordpres-posts-screen-to-only-show-authors-their-own-posts/}
	 */
	function ruc_parse_query_useronly( $wp_query ) {
	    if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/edit.php' ) !== false ) {
	        if ( !current_user_can( 'update_core' ) ) {
	            global $current_user;
	            $wp_query->set( 'author', $current_user->id );
	        }
	    }
	}


	/**
	 * Filter the media uploader simlar to the pre_get_post
	 */
	function ruc_ajax_attachments_useronly( $query ) {
		
		if( !current_user_can( 'update_core' ) ) {

			global $current_user;

			$query['author__in'] = array( $current_user->id, '1' );
		}

		return $query;
	}

	

	

	//=================
	// SETTINGS PAGE
	//=================
	/**
	 * Install
	 *
	 * Required by the interface - can be stubbed out if nothing is required on activation
	 * @used-by register_activation_hook() in the parent class
	 */
	function mp_plugin_install() {

		if( $this->_has_settings_page ) {

			//look for the settings
			$settings = get_option($this->_settings_name);
			if(!$settings) {
				add_option( $this->_settings_name, $this->_default_settings );
			}else{
				$old_settings = get_option( $this->_settings_name );
				$updated_settings = wp_parse_args( $_POST[$this->_settings_name], $this->_default_settings );
				update_option( $this->_settings_name, $updated_settings );
			}
		}
	}
	
	
	/**
	 * Settings Page Meta Boxes
	 *
	 * Hook to create the settings meta boxes
	 * Required by the interface 
	 * 
	 * @used-by add_meta_boxes_settings_page_{$this->_pagename} action  in the parent class
	 */
	function mp_plugin_create_meta_boxes() {

		//debug area
		add_meta_box(
			'debug_area', //Meta box ID
			__('Debug', 'ruc'), //Meta box Title
        array(&$this, 'mp_render_debug_setting_box'), //Callback defining the plugin's innards
        'settings_page_'.$this->_pagename, // Screen to which to add the meta box
        'side' // Context
    	);

	}

	/**
	 * Render the debug meta box
	 */
	function mp_render_debug_setting_box() {
		$settings = ( $option = get_option($this->_settings_name) ) ? $option : $this->_default_settings;
		?>
		<table class="form-table">
			<tr>
				<td colspan="2">
					<textarea class="widefat" rows="10"><?php print_r( $settings );?></textarea>
				</td>
			</tr>
		</table>
		<?php
	}
	
	/**
	 * Method to save the  settings
	 *
	 * Saves the settings 
	 * Required by the interface 
	 *
	 * @used-by Custom action "mp_plugin_save_options" in the parent class
	 */
	function mp_plugin_save_settings() {
		//lets just make sure we can save
		if ( !empty($_POST) && check_admin_referer( "{$this->_pagename}_save_settings", "{$this->_pagename}_settings_nonce" ) ) {
			//save
			if( isset( $_POST['submit'] ) ) {
				//status message
				$old_settings = get_option( $this->_settings_name );
				$updated_settings = wp_parse_args( $_POST[$this->_settings_name], $old_settings );
				update_option($this->_settings_name, $updated_settings);
				printf('<div class="updated"> <p> %s </p> </div>', __('Settings Saved', 'ruc' ) );
			}
			
			//reset
			if( isset( $_POST['reset'] ) ) {
				//status message
				update_option($this->_settings_name, $this->_default_settings );
				printf('<div class="error"> <p> %s </p> </div>', __('Settings reset to defaults', 'ruc') );
			}
		}
	}
	
	
	/**
	 * Filters the name of the settings page
	 * uses the custom filter "mp_settings_page_title"
	 */
	function mp_settings_page_title_filter($title) {
		return __('Restrict User Content Settings', 'ruc');
	}
}


//create an instance of the class
new Restrict_User_Content();

endif;