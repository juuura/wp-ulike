<?php
/**
 * Wp ULike FrontEnd Scripts Class.
 * // @echo HEADER
*/

// no direct access allowed
if ( ! defined('ABSPATH') ) {
    die();
}

if ( ! class_exists( 'wp_ulike_frontend_assets' ) ) {
	/**
	 *  Class to load and print the front-end scripts
	 */
	class wp_ulike_frontend_assets {

	  	private $hook;

	  	/**
	   	 * __construct
	   	 */
	  	function __construct() {
	    	// If user has been disabled this page in options, then return.
			if( ! is_wp_ulike( wp_ulike_get_setting( 'wp_ulike_general', 'plugin_files') ) ) {
				return;
			}
	        // general assets
	        $this->load_styles();
	        $this->load_scripts();
	  	}


	  	/**
	  	 * Styles for admin
	   	 *
	   	 * @return void
	   	 */
	  	public function load_styles() {

	        // @if DEV
	        /*
	        // @endif
	        wp_enqueue_style( 'wp-ulike', WP_ULIKE_ASSETS_URL . '/css/wp-ulike.min.css', array(), WP_ULIKE_VERSION );
	        // @if DEV
	        */
	        // @endif
	        // @if DEV
			wp_enqueue_style( 'wp-ulike', WP_ULIKE_ASSETS_URL . '/css/wp-ulike.css', array(), WP_ULIKE_VERSION );
	        // @endif

			//add your custom style from setting panel.
			wp_add_inline_style( 'wp-ulike', wp_ulike_get_custom_style() );

	  	}

	    /**
	     * Scripts for admin
	     *
	     * @return void
	     */
	  	public function load_scripts() {

	        // @if DEV
	        /*
	        // @endif
	        //Add wp_ulike script file with special functions.
	        wp_enqueue_script( 'wp_ulike', WP_ULIKE_ASSETS_URL . '/js/wp-ulike.min.js', array( 'jquery' ), WP_ULIKE_VERSION, true );
	        // @if DEV
	        */
	        // @endif
	        // @if DEV
			//Add wp_ulike script file with special functions.
			wp_enqueue_script( 'wp_ulike', WP_ULIKE_ASSETS_URL . '/js/wp-ulike.js', array( 'jquery' ), WP_ULIKE_VERSION, true );
	        // @endif

			//localize script
			wp_localize_script( 'wp_ulike', 'wp_ulike_params', array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'notifications' => wp_ulike_get_setting( 'wp_ulike_general', 'notifications')
			));
	  	}

	}

}