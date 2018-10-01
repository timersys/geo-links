<?php

/**
 * Helper class
 *
 * @package    Geotr
 * @subpackage Geotr/includes
 */
class Geol_Helper {
	
	/**
	 * Return the redirection options
	 * @param  int $id geotrcpt id
	 * @since  2.0
	 * @return array metadata values
	 */
	public static function get_options( $id ) {
		$defaults = array(
			'source_slug'	=> '',
			'dest'			=> array( 'dest_0' =>
									array(
										'url'		=> '',
										'ref'		=> '',
										'country'	=> '',
										'state'		=> '',
										'city' 		=> '',
										'device'	=> ''
									)
							),
		);

		$opts = wp_parse_args( get_post_meta( $id, 'geol_options', true ), $defaults );

		return apply_filters( 'geol/metaboxes/get_options', $opts, $id );
	}

	/**
	 * Return the devices options
	 * @param  
	 * @since  2.0
	 * @return array metadata values
	 */
	public static function get_devices() {
		return apply_filters('geol/devices/get_default', array(
				'mobiles'	=> __( "Mobile Phone", 'geol' ),
				'tablets'	=> __( "Tablet", 'geol' ),
				'desktop'	=> __( "Dekstop", 'geol' )
			));
	}
}
