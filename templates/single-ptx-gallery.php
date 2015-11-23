<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php
	$ptx_gallery_ids = ptx_get_gallery_ids();
	?>

	<?php foreach ( $ptx_gallery_ids as $ptx_image_id ) : ?>

		<?php
		$ptx_get_small_img_src     = wp_get_attachment_image_src( $ptx_image_id, 'ptx-preview' );
		$ptx_small_src             = $ptx_get_small_img_src[0];
		$ptx_image_attachment_page = get_attachment_link( $ptx_image_id );
		$ptx_download_link         = home_url() . '/api/download/' . $ptx_image_id;
		?>

		<p><a href="<?php echo $ptx_image_attachment_page; ?>"><img src="<?php echo $ptx_small_src; ?>"></a></p>
		<p><a href="<?php echo $ptx_image_attachment_page; ?>"><?php _e( 'Buy Print', 'ptx' ); ?></a> | <a href="<?php echo $ptx_download_link; ?>"><?php _e( 'Download', 'ptx' ); ?></a></p>

		<hr />
	<?php endforeach; ?>

<?php endwhile; ?>

<?php get_footer(); ?>