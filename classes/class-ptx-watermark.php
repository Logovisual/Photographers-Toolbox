<?php
/**
 * Watermark
 *
 * Watermark gallery photos on upload and save original in safe location.
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
 * Watermark
 *
 * Watermark gallery photos on upload and save original in safe location.
 *
 * @since 0.1.0
 */
class PTX_Watermark extends PTX_Shared {

	/**
	 * Construct
	 */
	function __construct( $domain ) {

		// Access shared resources
		parent::__construct();
	}
}