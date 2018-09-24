<?php

/**
 * Grab geol settings
 * @return mixed|void
 */
function geol_settings(){
	return apply_filters('geol/settings_page/opts', get_option( 'geol_settings' ) );
}