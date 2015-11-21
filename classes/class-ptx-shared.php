<?php
/**
 * Shared
 *
 * Shared functions, other classes extend this class to gain access.
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
 * Shared
 *
 * Commonly used plugin functions contained in its own class.
 *
 * @since 0.1.0
 */
class PTX_Shared {

	/**
	 * Text Domain
	 *
	 * @var string $domain
	 */
	protected $domain;

	/**
	 * Meta Boxes
	 */
	protected $meta_boxes;

	/**
	 * The plugin name
	 *
	 * @var string $plugin_name
	 */
	protected $plugin_name;

	/**
	 * Plugin Settings
	 *
	 * @var array $settings
	 */
	protected $settings;

	/**
	 * Construct
	 */
	function __construct() {

		// Get default settings
		$defaults = PTX_Shared::get_default_settings();

		$general   = wp_parse_args( get_option( 'ptx_options_general', $defaults['general'] ), $defaults['general'] );
		$watermark = wp_parse_args( get_option( 'ptx_options_watermark', $defaults['watermark'] ), $defaults['watermark'] );
		$thumbnail = wp_parse_args( get_option( 'ptx_options_thumbnail', $defaults['thumbnail'] ), $defaults['thumbnail'] );
		$pages     = wp_parse_args( get_option( 'ptx_options_pages', $defaults['pages'] ), $defaults['pages'] );

		$this->settings['general']   = $general;
		$this->settings['watermark'] = $watermark;
		$this->settings['thumbnail'] = $thumbnail;
		$thid->settings['pages']     = $pages;

	}

	/**
	 * Author meta box callback
	 *
	 * @param object $post
	 */
	function author_meta_box_cb( $post ) {

		global $user_ID;

		$user_args = array(
			'role'    => 'client',
			'orderby' => 'user_nicename'
		);

		$user_query_result = new WP_User_Query( $user_args );
		$clients = $user_query_result->get_results();

		if ( ! empty( $clients ) ) {
			foreach( $clients as $client ) {
				$client_authors[] = $client->ID;
			}
		}
		$authors = implode( ',', $client_authors );

		printf(
			'<label class="screen-reader-text" for="post_author_override">%s</label>',
			__( 'Assigned Client', $this->domain )
		);

		// TODO Change behavior when no client users are registered in WordPress
		wp_dropdown_users(
			array( 
				'include' => "$authors",
				'name' => 'post_author_override',
				'selected' => empty( $post->ID ) ? $user_ID : $post->post_author
			)
		);
		$screen = get_current_screen();

		if ( 'ptx-gallery' == $screen->post_type && 'edit.php?post_type=ptx-gallery' == $screen->parent_file && !isset( $_GET['action'] ) ) {

			$message = __( 'Always register your client before adding a gallery. Make sure you select the right client in the dropdown list above.', $this->domain );

			/**
			 * TODO FIXME Cleanup code, don't break out of php ( <?php ?> ) make it pretty. Include CSS snippet only on this screen.
			 */
			?>
		    <style type="text/css">
				.author_note {
					color: #9F6000;
					background-color: #FEEFB3;
					-webkit-border-radius: 3px;
					-moz-border-radius: 3px;
					border-radius: 3px;
					font-size: 13px;
					line-height: 1.5;
					margin: 10px 0 0 0;
					padding: 12px;
				}
				.author_note:before {
					float: left;
					font-family: "dashicons";
					content: "\f534";
					margin-right: 5px;
				}
			</style>
			<div class="author_note">
				<?php echo $message; ?>
			</div><?php
		}
	}	

	/**
	 * Create full path to original image
	 *
	 * @param string $file 
	 * @return string
	 */
	protected function create_original_image_path( $file ) {
		$storage_path = $this->settings['general']['storage_path'];
		$file_name = basename( $file, '.jpg' ); 
		return $storage_path . DIRECTORY_SEPARATOR . sanitize_file_name( $file_name ) . '.jpg';
	}

	/**
	 * Get default plugin settings
	 *
	 * @static
	 * @return array
	 */
	static function get_default_settings() {
		$default_settings = array(
			'general' => array(
				'storage_path' => '/enter/your/secure/server/path',
		   		'clients' => 0
			),
			'watermark' => array(
				'enable'   => 1,
				'image'    => 0,
				'position' => 'repeat',
				'position'   => 50
			),
			'thumbnail' => array(
		    	'width'  => 320,
		    	'height' => 240,
		    	'crop'   => false
			),
			'pages' => array(
		    	'login' => ''
			)
		);
		return $default_settings;
	}

	/**
	 * Get gallery IDs
	 *
	 * @return array
	 */
	protected function get_gallery_ids() {
		$attachment_ids = get_post_meta( get_the_ID(), '_ptx_image_gallery', true );
		$attachment_ids = explode( ',', $attachment_ids );
		return array_filter( $attachment_ids );
	}

	function get_png_images() {

		$attachments = get_posts( array( 'post_type' => 'attachment', 'post_mime_type' => 'image/png', 'post_parent' => 0, 'posts_per_page' => -1 ) );

		$png_media[0] = __( 'No watermark image selected.', $this->domain );

		foreach ( $attachments as $attachment ) {
			$png_media[ $attachment->ID ] = $attachment->post_title;
		}

		return $png_media;
	}

	/**
	 * Get post parent author ID from an attachment ID
	 *
	 * @param string $id 
	 * @return integer
	 */
	protected function get_parent_post_author_from_attachment_id( $id ) {
		$post = get_post( $id );
		if ( false === get_post_status( $id ) )
		{
			$this->get_error( 'invalid_post_parent' );
			return;
		} else {
			$parent_post = get_post( $post->post_parent );
			return $parent_post->post_author;
		}
	}

	/**
	 * Get post parent ID from an attachment ID
	 *
	 * @param string $id 
	 * @return integer|boolean
	 */
	protected function get_post_parent_id_from_attachment_id( $id ) {
		$post = get_post( $id );
		if ( $post ) {
			return $post->post_parent;
		}
		return false;
	}

	/**
	 * Set text domain
	 *
	 * @param string $domain The text domain used for translating.
	 */
	protected function set_domain( $domain ) {
		$this->domain = $domain;
	}
}