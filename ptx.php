<?php
/*
Plugin Name: Photographers Toolbox
Plugin URI:
Description: A plugin designed specifically for photographers. Private client proofing galleries. Watermark photos on upload. Protected digital downloads. Page templating compatible with any modern WordPress theme. Frontend login for clients.
Version: 0.1.0
Author:
Author URI:
Text Domain: ptx
Domain Path: /languages
*/

// Direct access not allowed.
if ( ! defined( 'WPINC' ) ) {
	die;
}

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

// Start the plugin
new PTX_Core( 'ptx' );
new PTX_Gallery;
new PTX_Download_API;