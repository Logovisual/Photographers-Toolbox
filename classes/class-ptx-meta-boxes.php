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
		
	}

	/**
	 * Author meta box callback
	 *
	 * @param object $post
	 */
	public function author_meta_box_cb( $post ) {

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
			__( 'Assigned Client', 'ptx' )
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

			$message = __( 'Always register your client before adding a gallery. Make sure you select the right client in the dropdown list above.', 'photography' );

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
}