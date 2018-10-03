<?php
/**
 * The redirect-facing functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/public
 */

/**
 * @package    Geol
 * @subpackage Geol/Redirect
 * @author     Damian Logghe <damian@timersys.com>
 */
class Geol_Redirects {

	/**
	 * The detected mobile or tablet.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private static $detect;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */

	public function __construct() {
		self::$detect = new Mobile_Detect;

		add_action( 'template_redirect', [ $this , 'redirect_link' ] );
	}

	/**
	 * Apply redirect
	 * @param 
	 * @since 1.0.0
	 */
	public function redirect_link() {

		if( is_singular( 'geol_cpt' ) ) {

			global $post;

			$country_name 	= geot_country_name();
			$city_name 		= geot_city_name();
			$state_name 	= geot_state_name();

			$geo_user = array('country' => $country_name, 'state' => $state_name, 'city' => $city_name);

			$opts = geol_options( $post->ID );
			
			foreach($opts['dest'] as $dest) {

				if( $this->geo_validation( $dest, $geo_user ) ) {
					wp_redirect($dest['url']);
					exit();
				}
			}
			
		}
	}


	/**
	 * conditional geo validation
	 * @param $dest is cpt values
	 * @param $geo is geot targeting
	 * @since 1.0.0
	 */
	private function geo_validation($dest, $geo) {
		
		$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

		// Ref
		if ( !empty($dest['ref']) && strpos( $referrer, $dest['ref'] ) === false )
			return false;

		//Devices
		if( $dest['device'] == 'mobiles' && !self::$detect->isMobile() )
			return false;

		if( $dest['device'] == 'tablets' && !self::$detect->isTablet() )
			return false;

		if( $dest['device'] == 'desktop' && ( self::$detect->isTablet() || self::$detect->isMobile() ) )
			return false;

		// Country
		if( !empty( $dest['country'] ) &&
			sanitize_title( $dest['country'] ) != sanitize_title( $geo['country'] )
		)	return false;

		// State
		if( !empty( $dest['state'] ) &&
			sanitize_title( $dest['state'] ) != sanitize_title( $geo['state'] )
		)	return false;

		// City
		if( !empty( $dest['city'] ) &&
			sanitize_title( $dest['city'] ) != sanitize_title( $geo['city'] )
		)	return false;

		return true;
	}
}

new Geol_Redirects();