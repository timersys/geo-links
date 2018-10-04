<?php

/**
 * Grab geol settings
 * @return mixed|void
 */
function geol_settings() {
	$defaults = apply_filters( 'geol/settings_page/defaults', [
			'ajax_mode' => '0',
			'goto_page' => 'goto',
		]
	);

	$opts = wp_parse_args( get_option( 'geol_settings' ), $defaults );

	return apply_filters( 'geol/settings_page/opts', $opts );
}


/**
 * Return the redirection options
 *
 * @param  int $id geol_cpt id
 *
 * @return array metadata values
 */
function geol_options( $id ) {
	$defaults = apply_filters( 'geol/metaboxes/defaults', [
			'source_slug' => '',
			'dest'        => [
				'dest_0' =>
					[
						'url'     => '',
						'ref'     => '',
						'country' => '',
						'state'   => '',
						'city'    => '',
						'device'  => '',
					],
			],
		]
	);

	$opts = wp_parse_args( get_post_meta( $id, 'geol_options', true ), $defaults );

	return apply_filters( 'geol/metaboxes/get_options', $opts, $id );
}

/**
 * Return the devices options
 * @return array metadata values
 */
function geol_devices() {
	return apply_filters( 'geol/devices/get_default', [
		'mobiles' => __( "Mobile Phone", 'geol' ),
		'tablets' => __( "Tablet", 'geol' ),
		'desktop' => __( "Dekstop", 'geol' ),
	] );
}