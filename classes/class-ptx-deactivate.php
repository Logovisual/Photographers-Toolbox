<?php
/**
 * Deactivate
 *
 * Fired during plugin deactivation.
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
 * Deactivate
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since 0.0.2
 */
class PTX_Deactivate {

	/**
	 * Deactivate
	 *
	 * @static
	 */
	static function deactivate() {
		$admin = get_role( 'administrator' );
		$admin->remove_cap( 'edit_gallery' );
		$admin->remove_cap( 'edit_galleries' );
		$admin->remove_cap( 'edit_other_galleries' );
		$admin->remove_cap( 'publish_galleries' );
		$admin->remove_cap( 'read_gallery' );
		$admin->remove_cap( 'read_private_galleries' );
		$admin->remove_cap( 'delete_gallery' );

		$client = get_role( 'client' );
		$client->remove_cap( 'read' );
		$client->remove_cap( 'read_gallery' );
		$client->remove_cap( 'read_private_galleries' );
		remove_role('client');
	} 
}