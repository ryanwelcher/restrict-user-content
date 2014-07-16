<?php
/**
 * @class 
 * 
 * Class to create virtual pages
 *
 */

if( !class_exists('MP_Virtual_Page') ) :

class MP_Virtual_Page {

	/**
	 * @var string : The url where the page appears
	 */ 
	private $_url;


	/**
	 * @var string : The query_var that we are looking for to load the template
	 */
	private $_query_var;

	/**
	 * $var array: Rewrite vars
	 */
	private $_rewrite_vars;

	/**
	 * @var string : The pageid this page is linked to
	 */
	private $_page_id;


	/**
	 * @var string :  The name of the template to load
	 */
	private $_template_file;


	/**
	 * __construct
	 *
	 * @param string the url string
	 * @param string the query_var to look for
	 * @param string the template we need to load
	 */

	function __construct( $url, $query_var, $page_id = false, $template_file = '', $rewrite_vars = false ) {

		$this->_url = $url;
		$this->_query_var = $query_var;
		$this->_page_id = $page_id;
		$this->_template_file = $template_file;
		$this->_rewrite_vars = $rewrite_vars;


		add_action( 'init', array(&$this, 'mp_virtual_page_add_rewrite_action') );
		
		//filter the query vars 
		add_filter( 'query_vars', array( &$this, 'mp_virtual_page_query_vars_filter' ) );
		
		//parse the request
		add_action( 'template_redirect', array(&$this, 'mp_virtual_page_assign_template_action' ) );
	}


	/**
	 * Create the rewrites for our virtual page
	 */
	function mp_virtual_page_add_rewrite_action() {

		$rewrite = 'index.php?'.$this->_query_var.'=1';

		if( $this->_page_id ){
			$rewrite .= '&page_id='.$this->_page_id;
		}

		$extra = '';
		//look for some rewrite vars and add them to the query vars we need
		if( $this->_rewrite_vars ) {
			for( $i = 0; $i < count( $this->_rewrite_vars ); $i ++) {
				$count = $i + 1;
				$extra .= "&{$this->_rewrite_vars[$i]}=\$matches[{$count}]";
			}
		}

		$rewrite .= $extra;
		add_rewrite_rule( $this->_url . '$', $rewrite, 'top' );
	}


	/**
	 * Filter the query vars
	 */
	function mp_virtual_page_query_vars_filter( $query_vars ) {
		$query_vars[] = $this->_query_var;

		if( $this->_rewrite_vars ) {
			for( $i = 0; $i < count( $this->_rewrite_vars ); $i ++) {
				$query_vars[] = $this->_rewrite_vars[$i];
			}
		}
		return $query_vars;
	}
	


	/**
	 * assign the correct template
	 */
	function mp_virtual_page_assign_template_action() {

		global $wp_query;
		
		//look for the existence of the query var
		if ( array_key_exists( $this->_query_var, $wp_query->query_vars ) ) {
			//look for the template in the theme directory

			$template = locate_template( array( $this->_template_file ), false , false);

			//include it
			if( $template ) {   
				include( $template );
			}else{
				echo 'No template found for MP_Virtual_Page : ' . $this->_url;
			}
			exit();
		}
		return;
	}
}

endif;