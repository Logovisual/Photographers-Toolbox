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

		$this->add_image_sizes();

		// Hook into WordPress
		add_action( 'admin_enqueue_scripts', array( $this, 'load_css') );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		$plugin_basename = plugin_basename( dirname( dirname( __FILE__ ) ).'/ptx.php' );
		add_filter( "plugin_action_links_$plugin_basename", array( $this, 'add_plugin_settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta' ), 10, 2 );

		// Load plugin page templates
		PTX_Page_Templates::load();
	}

	/**
	 * Add custom image sizes
	 */
	private function add_image_sizes() {

		$preview_width  = !isset( $this->settings['thumbnail']['width'] )  ? 320   : $this->settings['thumbnail']['width'];
		$preview_height = !isset( $this->settings['thumbnail']['height'] ) ? 240   : $this->settings['thumbnail']['height'];
		$preview_crop   = !isset( $this->settings['thumbnail']['crop'] )   ? false : $this->settings['thumbnail']['crop'];
		
		add_image_size( 'ptx-preview', $preview_width, $preview_height, $preview_crop );
		add_image_size( 'ptx-thumbnail-admin', 80, 80, true );
	}

	function add_plugin_meta( $links, $file ) {

		if ( strpos( $file, 'ptx.php' ) !== false ) {
			$new_links = array(
				'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=info%40kenthhagstrom%2ese&lc=SE&item_name=Photographers%20Toolbox%20WordPress%20Plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted">'.__( 'Donate', $this->domain ) . '</a>'
			);
			$links = array_merge( $links, $new_links );
		}
		return $links;
	}

	/**
	 * Add settings link to plugin
	 */
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