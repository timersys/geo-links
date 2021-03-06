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
class GeoLinks_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		GeotFunctions\add_countries_to_db();
		do_action('geotWP/activated');
	}

}
