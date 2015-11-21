<?php
/**
 * Meta Boxes
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
 * Meta Boxes
 *
 * 
 *
 * @since 0.1.0
 */
class PTX_Meta_boxes extends PTX_Shared {

	function __construct() {
		parent::__construct();

		// Hook into WordPress
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_gallery_meta' ) );
		add_action( 'admin_head', array( $this, 'gallery_css' ) );
	}

	function add_meta_boxes() {
		add_meta_box(
			'ptx-gallery-metabox',			      // ID
			__( 'Photo Gallery', $this->domain ), // Title
			array( $this, 'render_gallery_meta_box' ),		      // Callback
			'ptx-gallery',                        // Post type
			'normal',                             // Cotext
			'high'                                // Priority
		);
	}

	function gallery_css() { ?>
		<style>
			.ptx_gallery_images .details.attachment { box-shadow: none }
			.ptx_gallery_images .image > div { width: 80px; height: 80px; box-shadow: none; }
			.ptx_gallery_images .attachment-preview { position: relative; padding: 4px; }
			.ptx_gallery_images .attachment-preview .thumbnail { cursor: move }	
			.ptx_gallery_images .gallery-metabox-sortable-placeholder{width: 80px;height: 80px;box-sizing: border-box;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;border:4px dashed #ddd;background:#f7f7f7 url("<?php echo plugins_url( 'images/watermark.png', dirname(__FILE__) ); ?>") no-repeat center}		
			.ptx_gallery_images .ptx-gmb-remove {background: #eee url("<?php echo plugins_url( 'images/delete.png', dirname(__FILE__) ); ?>") center center no-repeat;position: absolute;top: 2px;right: 2px;border-radius: 2px;padding: 2px;display: none;width: 10px;height: 10px;margin: 0;display: none;overflow: hidden;}	
			.ptx_gallery_images .image div:hover .ptx-gmb-remove { display: block }
			.ptx_gallery_images:after, #ptx_gallery_images_container:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; }
			#ptx_gallery_images_container ul { margin: 0 !important }
			.ptx_gallery_images > li { float: left; cursor: move; margin: 9px 9px 0 0; }
			.ptx_gallery_images li.image img { width: 80px; height: 80px; }
			.ptx_gallery_images .attachment-preview:before { display: none !important; }
			
		</style>	
		<?php
	}

	function render_gallery_meta_box() {

		global $post; ?>

		<div id="ptx_gallery_images_container">
			<ul class="ptx_gallery_images">
				<?php
				$image_gallery = get_post_meta( $post->ID, '_ptx_image_gallery', true );
				$attachments = array_filter( explode( ',', $image_gallery ) );
				if ( $attachments ) {
					foreach ( $attachments as $attachment_id ) {
						if ( wp_attachment_is_image ( $attachment_id  ) ) {
							echo '<li class="image" data-attachment_id="' . $attachment_id . '"><div class="attachment-preview"><div class="thumbnail">
										' . wp_get_attachment_image( $attachment_id, 'ptx-thumbnail-admin' ) . '</div>
										<a href="#" class="ptx-gmb-remove" title="' . __( 'Remove image', 'ptx' ) . '"><div class="media-modal-icon"></div></a>
									</div></li>';
						}
					}
				} ?>
			</ul>
			<input type="hidden" id="image_gallery" name="image_gallery" value="<?php echo esc_attr( $image_gallery ); ?>" />
			<?php wp_nonce_field( 'ptx_image_gallery', 'ptx_image_gallery' ); ?>
		</div>
		<p class="add_ptx_gallery_images hide-if-no-js">
			<a href="#" class="button-primary"><?php _e( 'Add/Edit Images', 'ptx' ); ?></a>
		</p>
		<?php
		// options don't exist yet, set to checked by default
		if ( ! get_post_meta( get_the_ID(), '_ptx_image_gallery_link_images', true ) ) {
			$checked = ' checked="checked"';
		} else {
			$checked = checked( get_post_meta( get_the_ID(), '_ptx_image_gallery_link_images', true ), 'on', false );
		} ?>
		<p>
			<label for="ptx_image_gallery_link_images">
				<input type="checkbox" id="ptx_image_gallery_link_images" value="on" name="ptx_image_gallery_link_images"<?php echo $checked; ?> /> <?php _e( 'Enable Lightbox for this gallery?', 'ptx' )?>
			</label>
		</p>
		<?php // Props to WooCommerce for the following JS code ?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				// Uploading files
				var image_gallery_frame;
				var $image_gallery_ids	= $( '#image_gallery' );
				var $ptx_gallery_images 	= $( '#ptx_gallery_images_container ul.ptx_gallery_images' );
				jQuery( '.add_ptx_gallery_images' ).on( 'click', 'a', function( event ) {
					var $el = $(this);
					var attachment_ids = $image_gallery_ids.val();
					event.preventDefault();
					// If the media frame already exists, reopen it.
					if ( image_gallery_frame ) {
						image_gallery_frame.open();
						return;
					}
					// Create the media frame.
					image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
						// Set the title of the modal.
						title: "<?php _e( 'Add Images to Gallery', 'ptx' ); ?>",
						button: {
							text: "<?php _e( 'Add to gallery', 'ptx' ); ?>",
						},
						multiple: true
					});
					// When an image is selected, run a callback.
					image_gallery_frame.on( 'select', function( ) {
						var selection = image_gallery_frame.state().get('selection');
						selection.map( function( attachment ) {
							attachment = attachment.toJSON();
							if ( attachment.id ) {
								attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;
								 $ptx_gallery_images.append('\
									<li class="image" data-attachment_id="' + attachment.id + '">\
										<div class="attachment-preview">\
											<div class="thumbnail">\
												<img src="' + attachment.url + '" />\
											</div>\
										   <a href="#" class="ptx-gmb-remove" title="<?php _e( 'Remove Image', 'ptx' ); ?>"><div class="media-modal-icon"></div></a>\
										</div>\
									</li>');
							}
						} );
						$image_gallery_ids.val( attachment_ids );
					});
					// Finally, open the modal.
					image_gallery_frame.open();
				});
				// Image ordering
				$ptx_gallery_images.sortable({
					items					: 'li.image',
					cursor					: 'move',
					scrollSensitivity		: 40,
					forcePlaceholderSize	: true,
					forceHelperSize			: false,
					helper					: 'clone',
					opacity					: 0.65,
					placeholder				: 'gallery-metabox-sortable-placeholder',
					start:function( event,ui ) {
						ui.item.css( 'background-color', '#f6f6f6' );
					},
					stop:function( event,ui ){
						ui.item.removeAttr( 'style' );
					},
					update: function( event, ui ) {
						var attachment_ids = '';
						$( '#ptx_gallery_images_container ul li.image' ).css( 'cursor', 'default' ).each( function( ) {
							var attachment_id	= jQuery(this).attr( 'data-attachment_id' );
							attachment_ids		= attachment_ids + attachment_id + ',';
						});
						$image_gallery_ids.val( attachment_ids );
					}
				});
				// Remove images
				$( '#ptx_gallery_images_container' ).on( 'click', 'a.ptx-gmb-remove', function( ) {
					$( this ).closest( 'li.image' ).remove();
					var attachment_ids = '';
					$( '#ptx_gallery_images_container ul li.image' ).css( 'cursor', 'default' ).each( function( ) {
						var attachment_id	= jQuery( this ).attr( 'data-attachment_id' );
						attachment_ids		= attachment_ids + attachment_id + ',';
					} );
					$image_gallery_ids.val( attachment_ids );
					return false;
				} );
			});
		</script>
	<?php
	}

	function save_gallery_meta( $post_id ) {

		// Check nonce
		if ( ! isset( $_POST[ 'ptx_image_gallery' ] ) || ! wp_verify_nonce( $_POST[ 'ptx_image_gallery' ], 'ptx_image_gallery' ) ) {
			return;
		}
		// Check auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// Check user permissions
		$post_types = array( 'post' );
		if ( isset( $_POST['post_type'] ) && 'post' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}
		if ( isset( $_POST[ 'image_gallery' ] ) && !empty( $_POST[ 'image_gallery' ] ) ) {
			$attachment_ids = sanitize_text_field( $_POST['image_gallery'] );
			// Turn comma separated values into array
			$attachment_ids = explode( ',', $attachment_ids );
			// Clean the array
			$attachment_ids = array_filter( $attachment_ids  );

			$post_meta = get_post_meta( $post_id, '_original_files', true );
			$new_post_meta = array();
			
			if ( $post_meta )
			{
				foreach ( $post_meta as $k => $v )
				{
					if ( in_array( $k, $attachment_ids ) )
					{
						$new_post_meta[ $k ] = $v;
					}
				}
			}
			$post_meta = $new_post_meta;

			foreach ( $attachment_ids as $attachment_id ) :
			if ( ! array_key_exists( $attachment_id, $post_meta ) ) {
				$file = get_attached_file( $attachment_id );
				$post_meta[ $attachment_id ] = $this->create_original_image_path( $file );
			}
			endforeach;

			update_post_meta( $post_id, '_original_files', $post_meta );

			// Return back to comma separated list with no trailing comma. This is common when deleting the images
			$attachment_ids =  implode( ',', $attachment_ids );

			update_post_meta( $post_id, '_ptx_image_gallery', $attachment_ids );
			
			

		} else {
			// Delete gallery
			update_post_meta( $post_id, '_original_files', array() );
			delete_post_meta( $post_id, '_ptx_image_gallery' );
		}
		// link to larger images
		if ( isset( $_POST[ 'ptx_image_gallery_link_images' ] ) ) {
			update_post_meta( $post_id, '_ptx_image_gallery_link_images', $_POST[ 'ptx_image_gallery_link_images' ] );
		} else {
			update_post_meta( $post_id, '_ptx_image_gallery_link_images', 'off' );
		}

		do_action( 'ptx_gallery_save_metabox', $post_id );
	}
}