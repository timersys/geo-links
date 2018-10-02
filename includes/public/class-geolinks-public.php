<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Geotr
 * @subpackage Geotr/public
 */
//use GeotFunctions\Session\GeotSession;
//use function GeotFunctions\textarea_to_array;
//use function GeotWP\getUserIP;
//use function GeotWP\is_session_started;
//use Jaybizzle\CrawlerDetect\CrawlerDetect;

/**
 * @package    Geotr
 * @subpackage Geotr/public
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeoLinks_Public {

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private static $version;

	/**
	 * The IDs of all post.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $geolinks    The IDs of all post.
	 */
	private static $geolinks;

	private static $detect;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		self::$version 		= GEOL_VERSION;
		self::$geolinks 	= array();
		self::$detect 		= new Mobile_Detect;
	}

	/**
	* Add endpoint to rules
	* @param 
	* @since 1.0.0
	*/
	function add_endpoint() {
		global $wp, $wp_rewrite;

		$geolinks = (array)$this->get_geolinks();
		
		foreach( $geolinks as $opts ) {
			if( isset($opts['source_slug']) )
				add_rewrite_endpoint( $opts['source_slug'], EP_PAGES );
		}

		$wp_rewrite->flush_rules();
	}

	/**
	* Add query var
	* @param 
	* @since 1.0.0
	*/
	function endpoint_404($vars) {

		$settings = geol_settings();
		$geolinks = (array)$this->get_geolinks();

		foreach( $geolinks as $opts ) {
			if( isset($opts['source_slug']) )
				$vars[$opts['source_slug']] = true;
		}

		return $vars;
	}

	/**
	* Apply redirect
	* @param 
	* @since 1.0.0
	*/
	function endpoint_redirect() {

		$settings = geol_settings();

		if( is_page( $settings['goto_page'] ) ) {

			$country_name = geot_country_name();
			//$country_code = geot_country_code();
			$city_name = geot_city_name();
			$state_name = geot_state_name();
			//$state_code = geot_state_code();

			$geo_user = array('country' => $country_name, 'state' => $state_name, 'city' => $city_name);

			$geolinks = (array)$this->get_geolinks();
			
			foreach( $geolinks as $post_id => $opts ) {
				
				if( !isset($opts['source_slug']) || !get_query_var($opts['source_slug']) )
					continue;

				foreach($opts['dest'] as $dest) {

					if( $this->geo_validation( $dest, $geo_user ) ) {
						wp_redirect($dest['url']);
						exit();
					}
				}
			}
		}
	}

	/**
	* get all geolinks cpt
	* @param 
	* @since 1.0.0
	*/
	private function get_geolinks() {

		if( isset(self::$geolinks) && count(self::$geolinks) > 0 )
			return self::$geolinks;

		$query = wp_cache_get('geol_query');

		if( false === $query ) {

			$args = array( 'post_type' => 'geol_cpt', 'post_status' => 'publish' );
			$query = new WP_Query( $args );
			wp_cache_set( 'geol_query', $query );
		}

		if( $query->have_posts() ) {
			while( $query->have_posts() ) { $query->the_post();

				$opts = geol_options( get_the_ID() );
				self::$geolinks[get_the_ID()] = $opts;
			}
		}

		return self::$geolinks;
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