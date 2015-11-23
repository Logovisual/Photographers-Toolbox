<?php
/**
 * Page Templates
 *
 * Include page templates to enable easy theming.
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
 * Page Templates
 *
 * Include page templates to enable easy theming.
 *
 * @since 0.1.0
 */
class PTX_Page_Templates {

	private static $instance;
	protected $templates;

	public static function load() {
		if( null == self::$instance ) {
			self::$instance = new self();
		} 
		return self::$instance;
	}

    /**
     * Initialize plugin hijacking of page templates
     *
	 * @access private
     * @return void
     */
	private function __construct() {

		$this->templates = array();

		// Add a filter to the attributes metabox to inject template into the cache.
		add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'register_templates' ) );

		// NOTE change filter above to wp_dropdown_pages if not working with quick edit

		// Add a filter to the save post to inject out template into the page cache
		add_filter( 'wp_insert_post_data', array( $this, 'register_templates' ) );

		// Add a filter to the template include to determine if the page has our template assigned and return it's path
		add_filter( 'template_include',	array( $this, 'view_template' ) );

		// Add your templates to this array.
		$this->templates = array(
			'templates/login-page.php' => __( 'Custom Login Page', 'photography' ),
			'templates/account-details.php' => __( 'Account Details', 'photography' )
		);

		add_filter( 'template_include', array( $this, 'override_template_include' ), 10, 1 );
    }

	public function override_template_include( $single_template ) {

		global $post;

		if ( is_attachment() )
		{
			if ( $post->post_type == 'attachment' && get_post_type( $post->post_parent ) == 'ptx-gallery' )
			{
				if ( file_exists( get_stylesheet_directory().'/templates/ptx-image.php' ) )
				{
					return get_stylesheet_directory().'/templates/ptx-image.php';
				}
	        	return PTX_PATH . '/templates/ptx-image.php';
			}
		}
		if ( is_singular() )
		{
	    	if ( $post->post_type == 'ptx-gallery' && is_singular() )
			{
				if ( file_exists( get_stylesheet_directory().'/templates/single-ptx-gallery.php' ) )
				{
					return get_stylesheet_directory().'/templates/single-ptx-gallery.php';
				}
	        	return PTX_PATH . '/templates/single-ptx-gallery.php';
			}
		}
		return $single_template;
	}

	function register_templates( $atts ) {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        // Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if ( empty( $templates ) ) {
                $templates = array();
        } 

        // New cache, therefore remove the old one
        wp_cache_delete( $cache_key , 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge( $templates, $this->templates );

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $atts;
	}

	/**
	* Checks if the template is assigned to the page
	*/
	public function view_template( $template ) {

	    global $post;

		if ( !is_object( $post ) ) {
			return $template;
			exit;
		}
	    if ( !isset( $this->templates[ get_post_meta( $post->ID, '_wp_page_template', true ) ] ) ) {
			return $template;
		} 

		// Check if there is a them version of this page template before loading the plugin version
		if ( file_exists( get_stylesheet_directory().DIRECTORY_SEPARATOR.get_post_meta( $post->ID, '_wp_page_template', true ) ) )
		{
			$file = get_stylesheet_directory().DIRECTORY_SEPARATOR.get_post_meta( $post->ID, '_wp_page_template', true );
		} else {
			$file = PTX_PATH . get_post_meta( $post->ID, '_wp_page_template', true );
		}

	    // Just to be safe, we check if the file exist first
	    if( file_exists( $file ) ) {
			return $file;
		} 
		else { echo $file; }
		return $template;
	}
}