<?php
/**
 * Shared
 *
 * Shared functions, other classes extend this class to gain access.
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
 * Shared
 *
 * Commonly used plugin functions contained in its own class.
 *
 * @since 0.0.1
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
	 * Construct
	 */
	function __construct() {

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