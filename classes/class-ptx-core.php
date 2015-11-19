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
		parent::__construct();
		$this->set_domain( $domain );
		$this->set_locale();
		$this->plugin_name = __( 'Photographers Toolbox', $this->domain );
	}

	private function set_locale() {
		$i18n = new PTX_i18n;
		add_action( 'plugins_loaded', array( $i18n, 'load_textdomain' ) );
	} 
}