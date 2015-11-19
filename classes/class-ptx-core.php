<?php
/**
 * Core
 *
 * Base functions of the plugin.
 *
 * @package Photographers Toolbox
 * @subpackage Classes
 * @since 0.1.0
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
 * @since 0.1.0
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

		$plugin_basename = plugin_basename( dirname( dirname( __FILE__ ) ).'/ptx.php' );
		add_filter( "plugin_action_links_$plugin_basename", array( $this, 'add_plugin_settings_link' ) );
	}

	function add_plugin_settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=ptx_menu_page">' . __( 'Settings', $this->domain ) . '</a>';
		$links[] = $settings_link;
		return $links;
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