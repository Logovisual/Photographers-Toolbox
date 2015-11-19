<?php
/**
 * Gallery
 *
 * Register custom post type gallery.
 *
 * @package Photography Client Proofing
 * @subpackage Classes
 * @since 0.0.1
 */

// Direct access not allowed.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Gallery
 *
 * Register cutom post type gallery.
 *
 * @since 0.0.1
 */
class PCP_Gallery {

	/**
	 * Construct
	 */
	function __construct() {
		add_action( 'init', array( $this, 'initialize' ) );
		add_action( 'pre_get_posts', array( $this, 'change_default_admin_order' ) );
	}

	/**
	 * Change default admin order of post type
	 *
	 * @param object $wp_query The main query.
	 */
	function change_default_admin_order( $wp_query ) {
		if ( is_admin() && !isset( $_GET['orderby'] ) ) {

			// Get post type from the query
			$post_type = $wp_query->query['post_type'];

			// Apply ordering to the gallery post type
			if ( in_array( $post_type, array( 'photography-gallery' ) ) ) {
				$wp_query->set('orderby', 'date');
				$wp_query->set('order', 'DESC');
			}
			
			// Apply ordering to the gallery post type
			if ( in_array( $post_type, array( 'photography-print' ) ) ) {
				$wp_query->set('orderby', 'menu_order');
				$wp_query->set('order', 'ASC');
			}
		}
	}

	/**
	 * Initialize
	 */
	function initialize() {
		$this->register_post_type_gallery();
	}

	/**
	 * Register gallery post type
	 *
	 * @access private
	 */
	private function register_post_type_gallery() {

		// Labels for custom post type photography-gallery
		$labels = array(
			'name'               => _x( 'Galleries', 'post type general name', 'pcp' ),
			'singular_name'      => _x( 'Gallery', 'post type singular name', 'pcp' ),
			'add_new'            => _x( 'Add New', 'gallery', 'pcp' ),
			'add_new_item'       => __( 'Add New Gallery', 'pcp' ),
			'edit_item'          => __( 'Edit Gallery', 'pcp' ),
			'new_item'           => __( 'New Gallery', 'pcp' ),
			'all_items'          => __( 'All Galleries', 'pcp' ),
			'view_item'          => __( 'View Gallery', 'pcp' ),
			'search_items'       => __( 'Search Galleries', 'pcp' ),
			'not_found'          =>  __( 'No galleries found', 'pcp' ),
			'not_found_in_trash' => __( 'No galleries found in trash', 'pcp' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Galleries', 'pcp' )
		);

		// Args for custom post type photography-gallery
		$args = array(
			'labels'               => $labels,
			'public'               => true,
			'exclude_from_search'  => true,
			'publicly_queryable'   => true,
			'show_ui'              => true,
			'show_in_nav_menus'    => true,
			'show_in_menu'         => true,
			'query_var'            => true,
			'capabilities'         => array(
			        					'edit_post'          => 'edit_gallery',
			        					'edit_posts'         => 'edit_galleries',
			        					'edit_others_posts'  => 'edit_other_galleries',
			        					'publish_posts'      => 'publish_galleries',
			        					'read_post'          => 'read_gallery',
			        					'read_private_posts' => 'read_private_galleries',
			        					'delete_post'        => 'delete_gallery'
								   ),
			'has_archive'          => false,
			'hierarchical'         => true,
			'register_meta_box_cb' => 'pcp_gallery_meta_boxes',
			'map_meta_cap'         => true,
			'menu_icon'            => 'dashicons-format-gallery',
			'supports'             => array( 'title', 'editor', 'page-attributes', 'thumbnail', 'author', 'comments' ),
			'rewrite'              => array( 'slug' => _x( 'gallery', 'cpt gallery slug', 'pcp' ) )
		);

		// Add the post type
		register_post_type( 'pcp-gallery',$args );
	}
}