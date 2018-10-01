<?php

/**
 * Grab geol settings
 * @return mixed|void
 */
function geol_settings(){
	$defaults = [
			'ajax_mode'	=> '0',
			'goto_page'	=> 'goto',
			'goto_url'	=> site_url('goto')
		];
	$opts = wp_parse_args( get_option( 'geol_settings' ),  $defaults );
	
	return apply_filters('geol/settings_page/opts', $opts );
}