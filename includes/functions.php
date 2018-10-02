<?php

/**
 * Grab geol settings
 * @return mixed|void
 */
function geol_settings(){
	$defaults = apply_filters( 'geol/settings_page/defaults', array(
								'ajax_mode'	=> '0',
								'goto_page'	=> 'goto',
								'goto_url'	=> site_url('goto')
							)
						);

	$opts = wp_parse_args( get_option( 'geol_settings' ),  $defaults );
	
	return apply_filters('geol/settings_page/opts', $opts );
}


/**
 * Return the redirection options
 * @param  int $id geol_cpt id
 * @return array metadata values
 */
function geol_options( $id ) {
	$defaults = apply_filters( 'geol/metaboxes/defaults', array(
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
											)
						)
					);

	$opts = wp_parse_args( get_post_meta( $id, 'geol_options', true ), $defaults );

	return apply_filters( 'geol/metaboxes/get_options', $opts, $id );
}

/**
 * Return the devices options
 * @return array metadata values
 */
function geol_devices() {
	return apply_filters('geol/devices/get_default', array(
			'mobiles'	=> __( "Mobile Phone", 'geol' ),
			'tablets'	=> __( "Tablet", 'geol' ),
			'desktop'	=> __( "Dekstop", 'geol' )
		));
}