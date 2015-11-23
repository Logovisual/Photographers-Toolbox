<?php

function ptx_get_gallery_ids( $post_id = false ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$attachment_ids = get_post_meta( $post_id, '_ptx_image_gallery', true );
	$attachment_ids = explode( ',', $attachment_ids );
	return array_filter( $attachment_ids );
}