<?php
/**
 * Watermark
 *
 * Watermark gallery photos on upload and save original in safe location.
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
 * Watermark
 *
 * Watermark gallery photos on upload and save original in safe location.
 *
 * @since 0.1.0
 */
class PTX_Watermark extends PTX_Shared {

	/**
	 * Construct
	 */
	function __construct() {

		// Access shared resources
		parent::__construct();

		if ( isset( $this->settings['watermark']['enable'] ) ) {

			// Hook into WordPress
			add_filter( 'wp_generate_attachment_metadata', array( $this, 'watermark' ), 10, 2 );
		}
	}

	/**
	 * Add watermark to image
	 *
	 * @access public
	 * @param string $image
	 */
	public function add_watermark_to_( $image ) {

		// TODO Select graphics library from plugin settings (this is GD, add Imagick)

		$watermark_file = get_attached_file( $this->settings['watermark']['image'] );

		$watermark = imagecreatefrompng( $watermark_file );
		$new_image = imagecreatefromjpeg( $image );

		$margin = ( $this->settings['watermark']['position'] ) ? $this->settings['watermark']['position'] : 30;

		$watermark_width  = imagesx( $watermark );
		$watermark_height = imagesy( $watermark );
		$new_image_width  = imagesx( $new_image );
		$new_image_height = imagesy( $new_image );

		if ( $this->settings['watermark']['position'] == 'topleft' ) {
			$x_pos = $margin;
			$y_pos = $margin;
		} elseif ( $this->settings['watermark']['position'] == 'topright' ) {
			$x_pos = $new_image_width - $watermark_width - $margin;
			$y_pos = $margin;
		} elseif ( $this->settings['watermark']['position'] == 'bottomleft' ) {
			$x_pos = $margin;
			$y_pos = $new_image_height - $watermark_height - $margin;
		} elseif ( $this->settings['watermark']['position'] == 'bottomright' ) {
			$x_pos = $new_image_width - $watermark_width - $margin;
			$y_pos = $new_image_height - $watermark_height - $margin;
		} else {
			$x_pos = ( $new_image_width / 2 ) - ( $watermark_width / 2 );
			$y_pos = ( $new_image_height / 2 ) - ( $watermark_height / 2 );
		}

		if ( $this->settings['watermark']['position'] == 'repeat' ) {
			imagesettile( $new_image, $watermark );
			imagefilledrectangle( $new_image, 0, 0, $new_image_width, $new_image_height, IMG_COLOR_TILED );

		} else {
			imagecopy( $new_image, $watermark, $x_pos, $y_pos, 0, 0, $watermark_width, $watermark_height );				
		}

		$success = imagejpeg( $new_image, $image, 100 );
		imagedestroy( $new_image );
	}

	/**
	 * Add watermark to full size image and secure an original copy without a watermark
	 *
	 * @access private
	 * @param integer $attachment_id 
	 */
	private function add_watermark( $attachment_id )
	{
		$attachment = get_post( $attachment_id );

		if ( get_post_type( $attachment->post_parent ) == 'ptx-gallery' && $this->settings['watermark']['image'] ) {

			$watermark_file      = get_attached_file( $this->settings['watermark']['image'] );
			$watermark_file_type = wp_check_filetype( $watermark_file );

			if ( $watermark_file_type['ext'] == 'png' )
			{
				$file = get_attached_file( $attachment_id, 'full' );
				$this->copy_original( $file, $attachment_id );
				$this->add_watermark_to_( $file );
			} else {
				die( 'Watermark is not a png file' );
			}
		}
	}

	/**
	 * Copy original file to secure location
	 *
	 * @param string $file
	 * @param integer $attachment_id
	 */
	private function copy_original( $file, $attachment_id ) {

		// Get gallery post parent id
		$attachment = get_post( $attachment_id );
		$parent_id = $attachment->post_parent;

		$path_to_original_file = $this->create_original_image_path( $file );

		// Prevent overwrite when regenerating images, check if an originl photo already exists
		if ( ! file_exists( $path_to_original_file ) ) {
			copy( $file, $path_to_original_file );
		}

		// Get already stored post meta data
		$post_meta = get_post_meta( $parent_id, '_original_files', true );

		// Add this image
		$post_meta[ $attachment_id ] = $path_to_original_file;

		// Save gallery post meta
		update_post_meta( $parent_id, '_original_files', $post_meta );
	}

	/**
	 * Watermark
	 *
	 * @param array $metadata 
	 * @param integer $attachment_id
	 * @return array
	 */
	function watermark( $metadata, $attachment_id ) {

		// Get WordPress upload path
		$upload_dir = wp_upload_dir();

		$ext = '';

		// Loop through all image sizes
		foreach ( $metadata['sizes'] as $size => $data ) {

			// TODO make use of in_array and settings api to determine what sizes to watermark and which to skip

			if ( 'ptx-thumbnail-admin' == $size || 'preview' == $size || 'medium' == $size || 'thumbnail' == $size || 'ptx-thumbnail' == $size || 'post-thumbnail' == $size ) {
				continue;
			} else {

				// Get uploaded file extension
				$ext = pathinfo( $data['file'], PATHINFO_EXTENSION );

				if ( 'jpg' == $ext ) {

					// Generate image with watermark applied
					$file = $upload_dir['path'] . DIRECTORY_SEPARATOR . $data['file'];
					$this->add_watermark_to_( $file );
				}
			}
		}

		if ( 'jpg' == $ext ) {

			// Secure a copy of the original full size image and watermark the accessible full size image
			$this->add_watermark( $attachment_id );
		}
		return $metadata;
	}
}