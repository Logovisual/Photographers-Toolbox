<?php
/**
 * Activate
 *
 * Functions fired on plugin activation.
 *
 * @package Photography Client Proofing
 * @subpackage Classes
 * @since 0.0.1
 */

// Direct access not allowed.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Activate
 *
 * Fired during plugin activation.
 *
 * @since 0.0.1
 */
class PTX_Activate {

	/**
	 * Activate
	 */
	static function activate() {
		global $wpdb;

		// Alter database to enable taxonomy term order by term_order
		$check = $wpdb->query("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'");

		if ( 0 == $check ) {

			// Add custom term_order to the terms table, enables sorting of taxonomy terms
			$wpdb->query( "ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'" );
		}

		// Add custom capabilities to administrators
		$admin = get_role( 'administrator' );
		$admin->add_cap( 'edit_gallery' );
		$admin->add_cap( 'edit_galleries' );
		$admin->add_cap( 'edit_other_galleries' );
		$admin->add_cap( 'publish_galleries' );
		$admin->add_cap( 'read_gallery' );
		$admin->add_cap( 'read_private_galleries' );
		$admin->add_cap( 'delete_gallery' );

		// Add custom role "Client"
		add_role( 'client',	__( 'Client', 'ptx' ) );

		// Add custom capabilities to the client role
		$client = get_role( 'client' );
		$client->add_cap( 'read' );
		$client->add_cap( 'read_gallery' );
		$client->add_cap( 'read_private_galleries' );
		$client->add_cap( 'edit_others_posts' ); // Workaround to let users see attachments of private posts 
	}
}