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
	 *
	 * Usage: [ptx_download id=XX class=class text="download link text"] where XX is he numerical id of the attachment to download.
	 *
	 * @param array $atts 
	 * @return string
	 */
	function get_photo_download_link( $atts ) {

		$atts = shortcode_atts( array(
			'id' => null,
			'class' => '',
			'text' => __( 'Download', $this->domain )
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

		$link = sprintf(
			'<a class="%1$s" href="%2$s">%3$s</a>',
			$atts['class'],
			home_url() . '/api/download/' . $id,
			$atts['text']
		);

		return $link;
	}
}