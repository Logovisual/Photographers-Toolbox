<?php
/**
 * Core
 *
 * Base functions of the plugin.
 *
 * @package Photographers Toolbox
 * @subpackage Classes
 * @since 0.0.1
 */

// Direct access not allowed.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Core
 *
 * Base functions of the plugin.
 *
 * @since 0.0.1
 */
class PTX_Core extends PTX_Shared {

	/**
	 * Construct
	 */
	function __construct( $domain ) {

		// Access shared resources
		parent::__construct();

		// Initial plugin setup
		$this->set_domain( $domain );
		$this->set_locale();
		$this->plugin_name = __( 'Photographers Toolbox', $this->domain );

		// Hook into WordPress
		add_action( 'admin_enqueue_scripts', array( $this, 'load_css') );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
	}

	/**
	 * Load CSS
	 */
	function load_css() {

		wp_register_style( 'ptx-admin-css', plugins_url( 'css/ptx-admin.css', dirname(__FILE__) ) );
		wp_enqueue_style( 'ptx-admin-css' );
		
	}

	/**
	 * Load JS
	 */
	function load_scripts() {

	}

	private function set_locale() {
		$i18n = new PTX_i18n;
		add_action( 'plugins_loaded', array( $i18n, 'load_textdomain' ) );
	} 
}