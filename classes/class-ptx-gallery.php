<?php
/**
 * Gallery
 *
 * Register custom post type gallery.
 *
 * @package Photographers Toolbox
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
class PTX_Gallery extends PTX_Shared {

	/**
	 * Construct
	 */
	function __construct() {

		// Access shared resources
		parent::__construct();

		// Hook into WordPress
		add_action( 'init', array( $this, 'initialize' ) );
		add_action( 'pre_get_posts', array( $this, 'change_default_admin_order' ) );
		add_filter( 'enter_title_here', array( $this, 'change_enter_title_text' ) );
	}

	/**
	 * Change default admin order of post type
	 *
	 * @param object $wp_query The main query.
	 */
	function change_default_admin_order( $wp_query ) {

		// Setup default order if not set
		if ( is_admin() && !isset( $_GET['orderby'] ) ) {

			// Get post type from wp_query
			$post_type = $wp_query->query['post_type'];

			// Apply ordering to the ptx-gallery post type
			if ( in_array( $post_type, array( 'photography-gallery' ) ) ) {
				$wp_query->set('orderby', 'date');
				$wp_query->set('order', 'DESC');
			}

			// Apply ordering to the ptx-gallery post type
			if ( in_array( $post_type, array( 'photography-print' ) ) ) {
				$wp_query->set('orderby', 'menu_order');
				$wp_query->set('order', 'ASC');
			}
		}
	}

	/**
	 * Change "enter title here" text
	 *
	 * @param string $input The placeholder text to display in the title input field. 
	 */
	public function change_enter_title_text( $input ) {
		$screen = get_current_screen();

		// Change the text if ptx-gallery post type
		if ( is_admin() && 'ptx-gallery' == $screen->post_type ) {
			return __( 'Enter a name for the gallery here', $this->domain );
		}
		return $input;
	}

	/**
	 * Initialize the plugin
	 */
	function initialize() {
		$this->register_post_type_gallery();
	}

	/**
	 * Register the gallery post type
	 *
	 * @access private
	 */
	private function register_post_type_gallery() {

		// Labels for custom post type photography-gallery
		$labels = array(
			'name'               => _x( 'Galleries', 'post type general name', $this->domain ),
			'singular_name'      => _x( 'Gallery', 'post type singular name', $this->domain ),
			'add_new'            => _x( 'Add New', 'gallery', $this->domain ),
			'add_new_item'       => __( 'Add New Gallery', $this->domain ),
			'edit_item'          => __( 'Edit Gallery', $this->domain ),
			'new_item'           => __( 'New Gallery', $this->domain ),
			'all_items'          => __( 'All Galleries', $this->domain ),
			'view_item'          => __( 'View Gallery', $this->domain ),
			'search_items'       => __( 'Search Galleries', $this->domain ),
			'not_found'          => __( 'No galleries found', $this->domain ),
			'not_found_in_trash' => __( 'No galleries found in trash', $this->domain ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Galleries', $this->domain )
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
			'register_meta_box_cb' => 'ptx_gallery_meta_boxes',
			'map_meta_cap'         => true,
			'menu_icon'            => 'dashicons-format-gallery',
			'supports'             => array( 'title', 'editor', 'page-attributes', 'thumbnail', 'author', 'comments' ),
			'rewrite'              => array( 'slug' => _x( 'gallery', 'cpt gallery slug', $this->domain ) )
		);

		// Register the post type
		register_post_type( 'ptx-gallery',$args );
	}
}