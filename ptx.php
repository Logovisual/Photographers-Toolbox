<?php
/*
Plugin Name: Photographers Toolbox ( PTX )
Plugin URI:
Description: A plugin designed specifically for photographers. Private client proofing galleries. Watermark photos on upload. Protected digital downloads. Page templating compatible with any modern WordPress theme. Frontend login for clients.
Version: 0.1.0
Author: Kenth Hagström
Author URI: http://kenthhagstrom.se
Text Domain: ptx
Domain Path: /languages
*/

// Direct access not allowed.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Store plugin path in constant
define( 'PTX_PATH', plugin_dir_path( __FILE__ ) );

require_once( PTX_PATH . 'includes/helpers.php' );
require_once( PTX_PATH . 'includes/template-tags.php' );

/**
 * Autoload
 *
 * Automagically include class files when requested.
 *
 * @since 0.1.0
 *
 * @param string $class_name The class name to load. 
 */
function ptx_autoloader( $class_name ) {
	if ( false !== strpos( $class_name, 'PTX' ) ) {

		// Construct the class file name
		$class_path = plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
		$class_file = 'class-' . str_replace( '_', '-', $class_name ) . '.php';
		$class_file = strtolower( $class_file );

		// Include the class file if it exists
		if ( is_file( $class_path . $class_file ) ) {
			require_once( $class_path . $class_file );
		}
	}
}
spl_autoload_register( 'ptx_autoloader' );

// Plugin activation/deactivation hooks
register_activation_hook( __FILE__, array( 'PTX_Activate', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'PTX_Deactivate', 'deactivate' ) );

// Start the plugin and its components
new PTX_Core( 'ptx' );
new PTX_Redirects;
new PTX_Gallery;
new PTX_Download_API;
new PTX_Watermark;
new PTX_Meta_Boxes;
new PTX_Shortcodes;
if ( is_admin() ) {
	new PTX_Options;
}