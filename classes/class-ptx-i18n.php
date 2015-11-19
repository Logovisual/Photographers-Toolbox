<?php
/**
 * i18n
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package Photographers Toolbox
 * @subpackage Classes
 * @since 0.0.2
 */

// Direct access not allowed.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * i18n
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since 0.0.2
 */
class PTX_i18n extends PTX_Shared {

	/**
	 * Construct
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	function load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), $this->domain );

		// wp-content/languages/photography/photography-sv_SE.mo
		load_textdomain( $this->domain, trailingslashit( WP_LANG_DIR ) . $this->domain . '/' . $this->domain . '-' . $locale . '.mo' );

		// wp-content/plugins/photography/languages/photography-sv_SE.mo
		load_plugin_textdomain( $this->domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
}