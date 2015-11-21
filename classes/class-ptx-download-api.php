<?php
/**
 * Download API
 *
 * The download API allows for protected downloads requiring user authentication.
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
 * Download API
 *
 * Protected digital downloads.
 *
 * @since 0.1.0
 */
class PTX_Download_API extends PTX_Shared {

	/**
	 * The API URL
	 *
	 * @var string $api
	 */
	protected $api;

	/**
	 * Construct
	 */
	function __construct() {

		// Access shared resources
		parent::__construct();

		// Dyamically set the hidden API URL
		$this->api = home_url('/') . 'download?id=';

		// Hook into WordPress
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
		add_action( 'parse_request', array( $this, 'sniff_requests' ), 0 );
		add_action( 'init', array( $this, 'add_endpoint' ), 0 );
	}

	/**
	 * Add public query vars
	 *
	 * @param array $vars List of current public query vars
	 * @return array $vars
	 */
	function add_query_vars( $vars ) {

		// Add query vars
		$vars[] = '__api';
		$vars[] = 'photo';

		return $vars;
	}

	/**
	 * Add API Endpoint
	 *
	 * This is where the magic happens - brush up on your regex skillz
	 */
	function add_endpoint() {
		add_rewrite_rule( '^api/download/?([0-9]+)?/?', 'index.php?__api=1&photo=$matches[1]','top' );
	}

	/**
	 * Delivery
	 *
	 * Deliver the requested file as a digital download by using php headers.
	 *
	 * @access private
	 * @param string $file 
	 */
	private function delivery( $file ) {
		header( 'Content-Description: File Transfer');
		header( 'Content-Type: application/octet-stream');
		header( 'Content-Disposition: attachment; filename=' . basename( $file ) );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Connection: Keep-Alive' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $file ) );
		readfile( $file );
		exit;
	}

	/**
	 * Send response
	 *
	 * This is where we check for user authentication and validate the authencity of the request.
	 * Then send a response back to the user.
	 *
	 * @param string $status
	 * @param integer $download_id
	 */
	protected function send_response( $status, $download_id = '' ) {

		switch ( $status )
		{
			case 'invalid':

				wp_die('invalid request');
				exit;

			break;
			case 'error':

				wp_die( 'unknown error' );
				exit;

			break;
			case 'success':
				
				$this->require_login();

				// Get and sanitize required IDs
				$download_id = absint( $download_id );
				$gallery_id  = $this->get_post_parent_id_from_attachment_id( $download_id );

				if ( $this->is_user_admin() || ( $this->get_parent_post_author_from_attachment_id( $download_id  ) == get_current_user_id() ) )
				{
					if ( $this->post_has_image_attachment( $gallery_id ) )
					{
						$post_meta = get_post_meta( $gallery_id, '_original_files', true );
			
						if ( is_array( $post_meta ) && array_key_exists( $download_id, $post_meta ) )
						{
							$file = $post_meta[ $download_id ];
							$this->delivery( $file );

						} else {

							wp_die( 'original file not found, you have not deleted it, have you?' );
							exit;
						}
					}
			
				} else {

					wp_die( 'unauthorized access' );
					exit;
				}

			break;
			default:

				wp_die( 'unknown error' );
				exit;

			break;
		}

	}

	/**
	 * Sniff Requests
	 *
	 * This is where we hijack all API requests
	 * If $_GET['__api'] is set, we kill WP and serve the request to a digital photo download
	 *
	 * @return die if API request
	 */
	function sniff_requests() {
		global $wp;

		// Check if this is an API request
		if ( isset( $wp->query_vars['__api'] ) ) {
			$this->handle_request();
			exit;
		}
	}

	/**
	 * Handle Requests
	 *
	 * This is where we send off for an intense photo bomb ;)
	 */
	protected function handle_request() {
		global $wp;

		$download_id = $wp->query_vars['photo'];

		if ( !$download_id ) {
			$this->send_response( 'invalid' );
		}

		if ( $download_id ) {
			$this->send_response( 'success', absint( $download_id ) );
		} else {
			$this->send_response( 'error' );
		}
	}
}