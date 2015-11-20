<?php
/**
 * Plugin Options
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
 * Plugin Options
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since 0.1.0
 */
class PTX_Options extends PTX_Shared {

	/**
	 * Construct
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Access shared resources
		parent::__construct();

		// Hook into WordPress
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'initialize' ) );
	}

	/**
	 * Add settings menu pages
	 */
	public function add_menu_page() {
		$page = add_menu_page(
			__( 'PTX Settings', $this->domain ), 
			__( 'PTX Settings', $this->domain ), 
			'manage_options', 
			'ptx_menu_page',
			array( $this, 'render' ),
			'dashicons-camera',
			85
		);
		//add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_css' ) );

		// Submenu general
		$page = add_submenu_page(
			'ptx_menu_page',
			__('General',$this->domain),
			__('General',$this->domain),
			'manage_options',
			'ptx_menu_page',
			array( $this, 'render' )
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_css' ) );

		// Submenu watermark
		$page = add_submenu_page(
			'ptx_menu_page',
			__('Watermark',$this->domain),
			__('Watermark',$this->domain),
			'manage_options',
			'ptx_menu_page_watermark',
			array( $this, 'render' )
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_css' ) );

		// Submenu thumbnail
		$page = add_submenu_page(
			'ptx_menu_page',
			__('Thumbnails',$this->domain),
			__('Thumbnails',$this->domain),
			'manage_options',
			'ptx_menu_page_thumbnails',
			array( $this, 'render' )
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_css' ) );

		// Submenu thumbnail
		$page = add_submenu_page(
			'ptx_menu_page',
			__('Pages',$this->domain),
			__('Pages',$this->domain),
			'manage_options',
			'ptx_menu_page_pages',
			array( $this, 'render' )
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_css' ) );

		// Submenu thumbnail
		$page = add_submenu_page(
			'ptx_menu_page',
			__('Debug',$this->domain),
			__('Debug',$this->domain),
			'manage_options',
			'ptx_menu_page_debug',
			array( $this, 'render' )
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_css' ) );
	}

	function enqueue_css()
	{
		wp_register_style( 'ptx-admin-options-css', plugins_url( 'css/ptx-admin-options.css', dirname(__FILE__) ) );
		wp_enqueue_style( 'ptx-admin-options-css' );
	}

	public function render() {

		$screen = get_current_screen();
		
		echo "<div class=\"wrap\">\n";
		printf( '<h2>%s</h2>', __( 'Photograpers Toolbox Options', $this->domain ) );
		echo '<p>';
		_e( 'Setting up the PTX plugin properly is important. Part because of security, part because of functionality.', 'ptx' );
		echo '</p>';

		settings_errors();
		
		echo "<h2 class=\"nav-tab-wrapper\">\n";
		
		$tab = 'general';

		switch( $screen->base ) {
			case 'ptx-settings_page_ptx_menu_page_watermark':
				$tab = 'watermark';
			break;
			case 'ptx-settings_page_ptx_menu_page_thumbnails':
				$tab = 'thumbnails';
			break;
			case 'ptx-settings_page_ptx_menu_page_pages':
				$tab = 'pages';
			break;
			case 'ptx-settings_page_ptx_menu_page_debug':
				$tab = 'debug';
			break;
			default:
				$tab = 'general';
			break;
		}
		
		printf( '<a href="%2$s" class="nav-tab%3$s">%1$s</a>', __( 'General', $this->domain ), '?page=ptx_menu_page', ( $tab == 'general' ? ' nav-tab-active' : '' ) );
		printf( '<a href="%2$s" class="nav-tab%3$s">%1$s</a>', __( 'Watermark', $this->domain ), '?page=ptx_menu_page_watermark', ( $tab == 'watermark' ? ' nav-tab-active' : '' ) );
		printf( '<a href="%2$s" class="nav-tab%3$s">%1$s</a>', __( 'Thumbnails', $this->domain ), '?page=ptx_menu_page_thumbnails', ( $tab == 'thumbnails' ? ' nav-tab-active' : '' ) );
		printf( '<a href="%2$s" class="nav-tab%3$s">%1$s</a>', __( 'Pages', $this->domain ), '?page=ptx_menu_page_pages', ( $tab == 'pages' ? ' nav-tab-active' : '' ) );
		printf( '<a href="%2$s" class="nav-tab%3$s">%1$s</a>', __( 'Debug', $this->domain ), '?page=ptx_menu_page_debug', ( $tab == 'debug' ? ' nav-tab-active' : '' ) );

		echo "</h2>\n";

		echo "<form method=\"post\" action=\"options.php\">\n";

		switch ( $tab ) {
			case 'general':
				settings_fields( 'ptx_options_general' );
				do_settings_sections( 'ptx_options_general' );
			break;
			case 'watermark':
				settings_fields( 'ptx_options_watermark' );
				do_settings_sections( 'ptx_options_watermark' );
			break;
			case 'thumbnails':
				settings_fields( 'ptx_options_thumbnail' );
				do_settings_sections( 'ptx_options_thumbnail' );
			break;
			case 'pages':
				settings_fields( 'ptx_options_pages' );
				do_settings_sections( 'ptx_options_pages' );
			break;
			case 'debug':
				settings_fields( 'ptx_options_debug' );
				do_settings_sections( 'ptx_options_debug' );
			break;
		}

		submit_button();

		echo "</form>\n";
		echo "</div>";
	}

	public function initialize() {

		wp_register_style( 'ptx-options-css', plugins_url( 'css/ptx-options.css', dirname(__FILE__) ) );
		
		/**
		 *  GENERAL SETTINGS
		 */

		register_setting(
			'ptx_options_general',     // Option group
			'ptx_options_general',     // Photography option name
			array( $this, 'sanitize_general' ) // Sanitize input
		);
		
		add_settings_section(
			'ptx_options_general_section', // Section ID
			'', // Title
			array( $this, 'ptx_general_section_info' ), // Callback
			'ptx_options_general' // Page
		);

		add_settings_field(
		    'jpeg_quality', // ID
		    __( 'JPEG Quality<p class="description">High value, high quality and bigger file size. Low value, shitty pics, but you will save bandwidth. Min/max 1-100.</p>', $this->domain ), // Title 
		    array( $this, 'general_jpeg_quality_cb' ), // Callback
		    'ptx_options_general', // Page
			'ptx_options_general_section' // Section           
		);

		add_settings_field(
		    'storage_path', // ID
		    __( 'Storage Path<p class="description">Enter a local server path where your original photos will be stored. Make sure it is a secure location not accessible for anyone.</p>', $this->domain ) . '</p>',
		    array( $this, 'general_storage_path_cb' ), // Callback
		    'ptx_options_general', // Page
			'ptx_options_general_section' // Section           
		);

		add_settings_field(
		    'clients', // ID
		    __( 'Clients<p class="description">Before you can add any galleries you must have registered at least one client.</p>', $this->domain ), // Title 
		    array( $this, 'general_clients_cb' ), // Callback
		    'ptx_options_general', // Page
			'ptx_options_general_section' // Section           
		);

		/**
		 *  WATERMARK SETTINGS
		 */
		
		register_setting(
			'ptx_options_watermark',     // Option group
			'ptx_options_watermark',     // Photography option name
			array( $this, 'sanitize_watermark' ) // Sanitize input
		);
		
		add_settings_section(
			'ptx_options_watermark_section', // Section ID
			'', // Title
			array( $this, 'ptx_watermark_section_info' ), // Callback
			'ptx_options_watermark' // Page
		);

		add_settings_field(
		    'enable', // ID
		    __( 'Enable', $this->domain ), // Title 
		    array( $this, 'watermark_enable_cb' ), // Callback
		    'ptx_options_watermark', // Page
			'ptx_options_watermark_section' // Section           
		);

		add_settings_field(
		    'image', // ID
		    __( 'Your Logo', $this->domain ), // Title 
		    array( $this, 'watermark_image_cb' ), // Callback
		    'ptx_options_watermark', // Page
			'ptx_options_watermark_section' // Section
		);

		add_settings_field(
		    'position', // ID
		    __( 'Position', $this->domain ), // Title 
		    array( $this, 'watermark_position_cb' ), // Callback
		    'ptx_options_watermark', // Page
			'ptx_options_watermark_section', // Section
			array(
				'topleft'     => __( 'Top Left', $this->domain ),
				'topright'    => __( 'Top Right', $this->domain ),
				'bottomleft'  => __( 'Bottom Left', $this->domain ),
				'bottomright' => __( 'Bottom Right', $this->domain ),
				'center'      => __( 'Center', $this->domain ),
				'repeat'      => __( 'Repeat', $this->domain )
			)
		);

		add_settings_field(
		    'margin', // ID
		    __( 'Margin', $this->domain ), // Title 
		    array( $this, 'watermark_margin_cb' ), // Callback
		    'ptx_options_watermark', // Page
			'ptx_options_watermark_section' // Section           
		);

		/**
		 *  THUMBNAIL SETTINGS
		 */

		register_setting(
			'ptx_options_thumbnail',     // Option group
			'ptx_options_thumbnail',     // Photography option name
			array( $this, 'sanitize_thumbnail' ) // Sanitize input
		);

		add_settings_section(
			'ptx_options_thumbnail_section', // Section ID
			'', // Title
			array( $this, 'ptx_thumbnail_section_info' ), // Callback
			'ptx_options_thumbnail' // Page
		);

		add_settings_field(
		    'width', // ID
		    __( 'Width', $this->domain ), // Title 
		    array( $this, 'thumbnail_width_cb' ), // Callback
		    'ptx_options_thumbnail', // Page
			'ptx_options_thumbnail_section' // Section
		);

		add_settings_field(
		    'height', // ID
		    __( 'Height', $this->domain ), // Title 
		    array( $this, 'thumbnail_height_cb' ), // Callback
		    'ptx_options_thumbnail', // Page
			'ptx_options_thumbnail_section' // Section
		);

		add_settings_field(
		    'crop', // ID
		    __( 'Crop', $this->domain ), // Title 
		    array( $this, 'thumbnail_crop_cb' ), // Callback
		    'ptx_options_thumbnail', // Page
			'ptx_options_thumbnail_section' // Section 
		);

		/**
		 *  DEBUG PAGE
		 */

		register_setting(
			'ptx_options_pages',     // Option group
			'ptx_options_pages',     // Photography option name
			array( $this, 'sanitize_pages' ) // Sanitize input
		);
		
		add_settings_section(
			'ptx_options_pages_section', // Section ID
			'', // Title
			array( $this, 'ptx_pages_section_info' ), // Callback
			'ptx_options_pages' // Page
		);

		add_settings_field(
		    'login', // ID
		    __( 'Login Page<p class="description">The numerical ID of the page you would like to use for client login.</p>', $this->domain ), // Title 
		    array( $this, 'pages_login_cb' ), // Callback
		    'ptx_options_pages', // Page
			'ptx_options_pages_section' // Section 
		);

		/**
		 *  DEBUG PAGE
		 */

		register_setting(
			'ptx_options_debug',     // Option group
			'ptx_options_debug',     // Photography option name
			array( $this, 'sanitize_debug' ) // Sanitize input
		);
		
		add_settings_section(
			'ptx_options_debug_section', // Section ID
			'', // Title
			array( $this, 'ptx_debug_section_info' ), // Callback
			'ptx_options_debug' // Page
		);
	}

	/**
	 * Sanitize general options fields
	 *
	 * @access public
	 * @param array $input
	 * @return array
	 */
	public function sanitize_general( $input )
	{
		return $input;
	}

	public function sanitize_watermark( $input ) {
		return $input;
	}

	public function sanitize_thumbnail( $input ) {
		return $input;
	}

	public function ptx_general_section_info() {

		//print __( '<p>This plugin stores all photos you upload on your server. You need to make sure your local storage directory is safe and secure. Register clients by adding them as a user with the "Client" role, then you assign clients to galleries on creation. Only assigned clients and administrators can download full size original photos from galleries, authentication is required to download. The download API is simple, enter your_url/api/# where # is a numeric image id, the API will then check for existence and if you are authorized to download the file.</p>', $this->domain );
	}

	public function ptx_watermark_section_info()
	{
		// print __( '<p>Adding a watermark to your image is in no way a foolproof copyright protection, but it makes it a little harder to steal your work. You must upload a transparent .png file to the media library to enable watermarking of images.</p>', $this->domain );
	}

	public function ptx_thumbnail_section_info()
	{

	}

	public function ptx_pages_section_info()
	{
		
	}

	function ptx_debug_section_info()
	{
		echo '<h3>Image Processing</h3>';
		echo '<ul>';
		if( extension_loaded( 'imagick' ) )
		{
		    echo '<li>Imagick loaded and running.</li>';
		} else {
			echo '<li>Imagick NOT present. You should install it, it will produce better images because it can handle color profiles.</li>';
		}
		if( extension_loaded( 'gd' ) )
		{
		    echo '<li>GD library loaded and functional. Not optimal, but it works.</li>';
		} else {
			echo '<li>GD library NOT present.</li>';
		}
		echo '</ul>';
		
		echo '<h3>Current Plugin Settings</h3>';
		echo '<ul>';
		foreach ( $this->settings as $data ) {
			foreach ( $data as $key => $value ) {
				print '<li>'.$key.' : '.$value.'</li>';
			}
		}
		echo '</ul>';

		$ptx_installed = get_option( 'ptx_plugin_installed' );
		
		if ( $ptx_installed ) {
			echo '<p>PTX Marked as installed.</p>';
		} else {
			echo '<p>PTX <strong>not</strong> marked as installed!</p>';
		}
	}

	public function watermark_enable_cb()
	{
		$checked = !isset( $this->settings['watermark']['enable'] ) ? 0 : $this->settings['watermark']['enable'];

		print '<input type="checkbox" name="ptx_options_watermark[enable]" value="1" '.checked( $checked, 1, false ).'  />';
		print '<label for="enable">'.__( 'Your watermark logo will be added to all photos uploaded to client galleries.', $this->domain ).'</label>';
	}

	public function watermark_image_cb()
	{
     	$selected = !isset( $this->settings['watermark']['image'] ) ? 0 : $this->settings['watermark']['image'];
		$disabled = !isset( $this->settings['watermark']['enable'] ) ? ' disabled' : '';

		$png_files = $this->get_png_images();
		print '<select name="ptx_options_watermark[image]" id="watermark_image"'.$disabled.'>';
		
		foreach ( $png_files as $id => $title ) {
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				$id,
				selected( $id, $selected, false ),
				'ID: '. $id . ' - '.$title
			);
		}
		
		print '</select>';

		if ( $selected != 0 && isset( $this->settings['watermark']['image'] ) ) {
			printf(
				'<p class="description%s">%s</p>',
				' success',
				__( 'You have selected a valid .png file.', $this->domain )
			);
		} else {
			printf(
				'<p class="description%s">%s</p>',
				' fail',
				__( 'You have not selected a valid .png file.', $this->domain )
			);
		}

	}

	public function watermark_margin_cb()
	{
		$margin = !isset( $this->settings['watermark']['margin'] ) ? 50: $this->settings['watermark']['margin'];
		$disabled = !isset( $this->settings['watermark']['enable'] ) ? ' disabled' : '';

		print '<input class="regular-text ltr" name="ptx_options_watermark[margin]" id="watermark_margin" value="'.$margin.'"'.$disabled.' />';
		print '<p class="description">'.__('The margin from edge of image.',$this->domain).'</p>';
	}

	public function watermark_position_cb( $args )
	{
		$selected = !isset( $this->settings['watermark']['position'] ) ? 'repeat' : $this->settings['watermark']['position'];
		$disabled = !isset( $this->settings['watermark']['enable'] ) ? ' disabled' : '';

		print '<select name="ptx_options_watermark[position]" id="watermark_position" type="text"'.$disabled.' />';
		
		foreach ( $args as $id => $position ) {
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				$id,
				selected( $id, $selected, false ),
				$position
			);
		}
		
		print '</select>';
	}

	public function general_jpeg_quality_cb()
	{
		$jpeg_quality = !isset( $this->settings['general']['jpeg_quality'] ) ? 100: $this->settings['general']['jpeg_quality'];

		print '<input class="regular-text ltr" name="ptx_options_general[jpeg_quality]" id="general_jpeg_quality" value="'.$jpeg_quality.'" />';
		print '<p class="description">'.__('Min/Max: 1-100. Set JPEG quality for images being upload.',$this->domain).'</p>';

	}

	public function general_storage_path_cb()
	{
		$storage_path = !isset( $this->settings['general']['storage_path'] ) ?  '': $this->settings['general']['storage_path'];
		
		print '<input class="regular-text ltr" name="ptx_options_general[storage_path]" id="watermark_margin" value="'.$storage_path.'" />';
		print '<p class="description">';
		_e( 'Document Root: ', $this->domain );
		print esc_url( $_SERVER["DOCUMENT_ROOT"] );
		print '</p>';
		
		// Check that the directory is writeable
		if ( is_writable( $storage_path ) ) {

			$fileperms = substr( sprintf( '%o', fileperms( $storage_path ) ), -4 );

			printf(
				'<p class="description%s">%s %s</p>',
				' success',
				__( 'The storage directory path is writable. Permissions set to: ', $this->domain ),
				$fileperms
			);
		} else {
			printf(
				'<p class="description%s">%s</p>',
				' fail',
				__( 'The storage directory path is not writable.', $this->domain )
			);
		}
	}

	public function general_clients_cb() {
	
		$options = get_option( 'ptx_options_general' );

		$args = array(
			'role'    => 'client',
			'orderby' => 'user_nicename'
		);
		$user_query_result = new WP_User_Query( $args );
		$clients = $user_query_result->get_results();

		if ( ! empty( $clients ) ) {
			printf(
				'<p class="description%s">%s</p>',
				' success',
				__( 'You have clients registered.', $this->domain )
			);
			$data = true;
			
			echo '<table>';
			echo '<tr>';
			echo '<th>Name</th>';
			echo '<th>E-mail</th>';
			echo '<th>Description</th>';
			echo '</tr>';
			foreach( $clients as $client ) {
				echo '<tr>';
				$author_info = get_userdata($client->ID);
				echo '<td>'.$client->user_nicename.'</td>';
				echo '<td>'.$author_info->user_email.'</td>';
				echo '<td>'.$author_info->description.'</td>';
				echo '</tr>';
			}
			echo '</table>';
		} else {
			printf(
				'<p class="description%s">%s</p>',
				' fail',
				__( 'You have no clients registered.', $this->domain )
			);
			$data = false;
		}

		print '<input type="hidden" class="regular-text ltr" name="ptx_options_general[clients]" id="watermark_margin" value="'.$data.'" />';
		
	}

	public function thumbnail_width_cb()
	{
		$width = !isset( $this->settings['thumbnail']['width'] ) ? 320 : $this->settings['thumbnail']['width'];

		print '<input class="regular-text ltr" name="ptx_options_thumbnail[width]" id="thumbnail_width" value="'.$width.'" />';
		print '<p class="description">'.__('Thumbnail width in pixels.',$this->domain).'</p>';
	}

	public function thumbnail_height_cb()
	{
		$height = !isset( $this->settings['thumbnail']['height'] ) ? 240 : $this->settings['thumbnail']['height'];

		print '<input class="regular-text ltr" name="ptx_options_thumbnail[height]" id="thumbnail_height" value="'.$height.'" />';
		print '<p class="description">'.__('Thumbnail height in pixels.',$this->domain).'</p>';
	}

	public function thumbnail_crop_cb()
	{
		$crop = !isset( $this->settings['thumbnail']['crop'] ) ? 'false' : $this->settings['thumbnail']['crop'];
 
		// TODO Image crop has more options then true/false, add them for more advanced functionality.
		print '<input class="regular-text ltr" name="ptx_options_thumbnail[crop]" id="thumbnail_crop" value="'.$crop.'" />';
		print '<p class="description">'.__('Thumbnail crop.',$this->domain).'</p>';
	}

	public function pages_login_cb()
	{
		$login_page_id = !isset( $this->settings['pages']['login'] ) ? 100: $this->settings['pages']['login'];

		print '<input class="regular-text ltr" name="ptx_options_pages[login]" id="pages_login" value="'.$login_page_id.'" />';
		print '<p class="description">'.__('Select page for login.',$this->domain).'</p>';

	}
}