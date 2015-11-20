<?php
/**
 * Gallery
 *
 * Register custom post type gallery.
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
 * Gallery
 *
 * Register cutom post type gallery.
 *
 * @since 0.1.0
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
		add_action( 'admin_menu', array( $this, 'replace_author_meta_box' ), 10, 1 );
		add_filter( 'enter_title_here', array( $this, 'change_enter_title_text' ) );
		add_action( 'post_submitbox_misc_actions' , array( $this, 'post_submitbox_change_visibility' ) );
		add_action( 'add_attachment', array( $this, 'set_attachment_author_to_post_author' ), 10, 1 );
		add_filter( 'dashboard_glance_items', array( $this, 'glance_items' ), 10, 1 );

		// Custom columns for the ptx-gallery post type
		add_filter( 'manage_edit-ptx-gallery_columns',          array( $this, 'edit_ptx_gallery_columns' ) );
		add_filter( 'manage_edit-ptx-gallery_sortable_columns', array( $this, 'edit_ptx_gallery_sortable_columns' ) );
		add_action( 'manage_ptx-gallery_posts_custom_column',   array( $this, 'manage_ptx_gallery_posts_custom_column' ), 10, 2 );
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

	function edit_ptx_gallery_columns( $columns ) {
		$columns = array(
			'cb'                   => "<input type=\"checkbox\" />",
			'title'                => _x( 'Title', 'admin column name', 'ptx' ),
			'count_gallery_photos' => _x( 'Photos', 'admin column name', 'ptx' ),
			'gallery_thumbnail'    => _x( 'Gallery Thumbnails', 'admin column name', 'ptx' ),
			'author'               => _x( 'Client', 'admin column name', 'ptx' ),
			'comments'             => _x( 'Comments', 'admin column name', 'ptx' ),
			'date'                 => _x( 'Date', 'admin column name', 'ptx' ),
		);

		$columns['comments'] = '<div class="vers"><img alt="'. _x( 'Comments', 'image alt text', 'ptx' ) .'" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';

		return $columns;
	}

	function edit_ptx_gallery_sortable_columns( $columns ) {
		$columns['author'] = 'author';
		$columns['count_gallery_photos'] = 'count_gallery_photos';
		return $columns;
	}

	function manage_ptx_gallery_posts_custom_column( $columns, $post_id ) {

		switch ( $columns ) {

			case 'gallery_thumbnail':

				$ids = $this->get_gallery_ids();

				if ( count( $ids ) > 0 ) {
					$i=0;
					foreach( $ids as $id ) {

						$width = (int) 45;
						$height = (int) 35;
						$thumb = wp_get_attachment_image( $id, array($width, $height), true );

						if ( isset( $thumb ) ) {
							echo $thumb;
						} else {
							echo '—';
						}
						$i++;
						if( $i >= 5 ) break;
					}
				} else {
					echo '—';
				}
				
			break;
			case 'count_gallery_photos':
			
				$photos = $this->get_gallery_ids();
				$count = count( $photos );

				if ( $count >= 1 ) {
					echo $count;
				} else {
					echo '—';
				}

			break;
		}
	}

	/**
	 * Add objects to "at a glance" box
	 *
	 * @return array $items The items to display 
	 */
	public function glance_items( $items = array() ) {

		$post_types = array('ptx-gallery');
		foreach ( $post_types as $type )
		{
			if ( ! post_type_exists( $type ) ) continue;

			$num_posts = wp_count_posts( $type );
			
			if ( $num_posts ) {
				$published = intval( $num_posts->private );
				$post_type = get_post_type_object( $type );
				$text = _n( '%s Private ' . $post_type->labels->singular_name, '%s Private ' . $post_type->labels->name, $published, 'ptx' );
				$text = sprintf( $text, number_format_i18n( $published ) );
				$link = sprintf( __( '<a href="%1$s">%2$s</a>', 'ptx' ), 'edit.php?post_type='.$type, $text );
				$items[] = sprintf( '<span class="%1$s-count">%2$s</span>', $type, $link ) . "\n";

				$published = intval( $num_posts->publish );
				$post_type = get_post_type_object( $type );
				$text = _n( '%s Public ' . $post_type->labels->singular_name, '%s Public ' . $post_type->labels->name, $published, 'ptx' );
				$text = sprintf( $text, number_format_i18n( $published ) );
				$link = sprintf( __( '<a href="%1$s">%2$s</a>', 'ptx' ), 'edit.php?post_type='.$type, $text );
				$items[] = sprintf( '<span class="%1$s-count">%2$s</span>', $type, $link ) . "\n";
				
				$published = intval( $num_posts->pending );
				$post_type = get_post_type_object( $type );
				$text = _n( '%s ' . $post_type->labels->singular_name.' Pending Review', '%s ' . $post_type->labels->name.' Pending Review', $published, 'ptx' );
				$text = sprintf( $text, number_format_i18n( $published ) );
				$link = sprintf( __( '<a href="%1$s">%2$s</a>', 'ptx' ), 'edit.php?post_type='.$type, $text );
				$items[] = sprintf( '<span class="%1$s-count">%2$s</span>', $type, $link ) . "\n";
			}
		}
		return $items;
	}

	/**
	 * Initialize the plugin
	 */
	function initialize() {
		$this->register_post_type_gallery();
	}

	/**
	 * Change default gallery post Visibility to private
	 *
	 * Change the default post submitbox visiility value to Private. Display information
	 * about it below the submit button to notify user that it's being posted as private.
	 */
	function post_submitbox_change_visibility() {
	    global $post;

	    if ( 'ptx-gallery' != $post->post_type ) {
	        return;
		}

		$screen = get_current_screen();
		if ( 'ptx-gallery' == $screen->post_type && 'edit.php?post_type=ptx-gallery' == $screen->parent_file && !isset( $_GET['action'] ) ) {

		    $visibility = 'private';
		    $visibility_trans = __( 'Private', $this->domain );
			$message = __( 'New galleries are always set to <strong>private</strong> by default, to enforce a required login. Visitors not logged in will be redirected to the login page.', $this->domain );

			echo '<script type="text/javascript">';
	        echo '(function($){';
			echo 'try {';
			echo "$('#post-visibility-display').text('" . $visibility_trans . "');";
			echo "$('#hidden-post-visibility').val('" . $visibility . "');";
			echo "$('#visibility-radio-" . $visibility . "').attr('checked', true);";
			echo '} catch(err){}';
			echo '}) (jQuery);';
			echo '</script>';
			
			echo '<div class="publish_note">';
			echo $message;
			echo '</div>';
		}
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
			//'register_meta_box_cb' => 'ptx_gallery_meta_boxes',
			'map_meta_cap'         => true,
			'menu_icon'            => 'dashicons-format-gallery',
			'supports'             => array( 'title', 'editor', 'page-attributes', 'thumbnail', 'author', 'comments' ),
			'rewrite'              => array( 'slug' => _x( 'gallery', 'cpt gallery slug', $this->domain ) )
		);

		// Register the post type
		register_post_type( 'ptx-gallery',$args );
	}

	/**
	 * Replace author meta box
	 *
	 * Because we want our galleries to be "owned" by a client, not the author of it, we'll create
	 * a custom metabox where a gallery owner can be chosen.
	 */
	public function replace_author_meta_box() {
		remove_meta_box( 'authordiv', 'ptx-gallery', 'normal' );
		//remove_meta_box( 'authordiv', 'ptx-order', 'normal' );
		add_meta_box( 'ptx_authordiv', __( 'Client','ptx' ), array( $this->meta_boxes['ptx'], 'author_meta_box_cb' ), 'ptx-gallery', 'side', 'core' );
		//add_meta_box( 'ptx_authordiv', __( 'Client','ptx' ), array( 'PTX_Meta_Boxes', 'author_meta_box_cb' ), 'ptx-order', 'normal', 'core' );
	}

	/**
	 * Set attachment author to post author
	 */
	public function set_attachment_author_to_post_author( $attachment_id ) {

		$attach = get_post( $attachment_id );

		if ( $attach->post_parent ) {

			$parent = get_post( $attach->post_parent );

			$the_post = array();
			$the_post['ID'] = $attachment_id;
			$the_post['post_author'] = $parent->post_author;

		    wp_update_post( $the_post );
		}
	}
}