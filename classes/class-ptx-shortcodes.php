<?php

class PTX_Shortcodes extends PTX_Shared {

	function __construct() {

		// Make sure parent object constructor runs
		parent::__construct();

		add_shortcode( 'ptx_download', array( $this, 'get_photo_download_link' ) );
	}

	/**
	 * Get download link
	 *
	 * Creates and returns a download link for a ptx-gallery attachment.
	 * TODO User authentication should be made here...
	 *
	 * Usage: [ptx_download id=XX] where XX is he numerical id of the attachment to download.
	 *
	 * @param array $atts 
	 * @return string
	 */
	function get_photo_download_link( $atts ) {

		$atts = shortcode_atts( array(
			'id' => null
		), $atts );
		
		if ( $atts['id'] == null ) {
			return;
		}

		$id = absint( $atts['id'] );

		// Get parent id and check that it's an image attached to a gallery post
		$post_parent_id = $this->get_post_parent_id_from_attachment_id( $id );
		$post_type = get_post_type( $post_parent_id );

		if ( 'ptx-gallery' !== $post_type ) {
			return; // TODO Create a more intuitive response than nothing?
		}

		// Create the download link
		$link = home_url() . '/api/download/' . $id;

		return $link;
	}
}