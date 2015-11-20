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
	 * Set text domain
	 *
	 * @param string $domain The text domain used for translating.
	 */
	protected function set_domain( $domain ) {
		$this->domain = $domain;
	}
}