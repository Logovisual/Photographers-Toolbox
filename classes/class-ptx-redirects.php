<?php
/**
 * Redirects
 *
 *
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
 * Redirects
 *
 *
 *
 * @since 0.1.0
 */
class PTX_Redirects extends PTX_Shared {

	function __construct() {
		parent::__construct();

		// Hook into WordPress
		add_action( 'template_redirect', array( $this, 'private_content_redirect_to_login' ) );
	}

	/**
	 * Redirect user to login form when trying access a private post when not logged in
	 */
	public function private_content_redirect_to_login() {

		global $wp_query,$wpdb;

		$request = $wpdb->get_row( $wp_query->request );
		if ( isset( $request->post_parent ) ) {
			$status = get_post_status( $request->post_parent );
		}

		if ( is_user_logged_in() && is_single() && 'private' == $status ) {
			$current_user_id = get_current_user_id();
			$post_parent     = get_post( $request->post_parent );

			// Check if user is administrator
			if( current_user_can('manage_options') ) {

			} else {
				if ( $current_user_id != $post_parent->post_author ) {
					wp_die( 'You are not authorized. Wrong user ID.' );
					exit;
				}
			}
		}

		if (is_404() ) {
			$location = wp_login_url($_SERVER["REQUEST_URI"]);
			if ( $request->post_parent ) {
				$status = get_post_status( $request->post_parent );
			}
			
			if ( 'private' == $request->post_status || 'private' == $status ) {
				wp_safe_redirect($location);
				exit;
			}
		}
	}
}