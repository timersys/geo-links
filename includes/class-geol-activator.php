<?php

/**
 * Fired during plugin activation
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Geotr
 * @subpackage Geotr/includes
 * @author     Damian Logghe <damian@timersys.com>
 */
class Geol_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if( !get_page_by_path('goto') ) {
			$args = array(
							'post_type'		=> 'page',
							'post_title'	=> 'Goto',
							'post_name'		=> 'goto',
							'post_status'	=> 'publish'
						);
			
			wp_insert_post($args);
		}
	}

}
