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

		add_action( 'template_redirect', [ $this, 'redirect_link' ] );
		add_shortcode( 'geo-link' , [$this, 'add_shortcode'], 10, 2 );
	}

	/**
	 * Apply redirect
	 *
	 * @param
	 *
	 * @since 1.0.0
	 */
	public function redirect_link() {

		if ( is_singular( 'geol_cpt' ) ) {

			$post_id	= get_the_id();
			$opts 		= geol_options( $post_id );
			$settings 	= geol_settings();

			$this->count_click('click', $post_id);
			
			// check redirections to see if we have any match
			foreach ( $opts['dest'] as $key => $redirect ) {

				$redirect = apply_filters( 'geol/redirect_params', $redirect, $post_id );

				// validate redirect
				if ( $this->validate_redirection( $redirect ) ) {
					
					// last change to abort
					if ( apply_filters( 'geol/cancel_redirect', false, $redirect, $post_id ) ) {
						return;
					}

					$this->count_click('match', $post_id);
					$this->count_click('dest', $post_id, $key);

					wp_redirect( esc_url( $redirect['url'] ), $opts['status_code'] );
					exit();
				}
			}

		}
	}


	/**
	 * conditional geo validation
	 *
	 * @param $redirect is cpt values
	 * @param $geo is geot targeting
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	private function validate_redirection( $redirect ) {

		$referrer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';

		// Ref
		if ( ! empty( $dest['ref'] ) && strpos( $referrer, $redirect['ref'] ) === false ) {
			return false;
		}

		//Devices Mobiles
		if ( $redirect['device'] == 'mobiles' && self::$detect->isMobile() ) {
			return false;
		}

		//Devices Tablets
		if ( $redirect['device'] == 'tablets' && self::$detect->isTablet() ) {
			return false;
		}

		//Devices Desktop
		if ( $redirect['device'] == 'desktop' && ( ! self::$detect->isTablet() && ! self::$detect->isMobile() ) ) {
			return false;
		}

		// Country
		if ( ! empty( $redirect['countries'] ) && ! geot_target( $redirect['countries'] ) ) {
			return false;
		}

		// regions
		if ( ! empty( $redirect['regions'] ) && ! geot_target( '', $redirect['regions'] ) ) {
			return false;
		}

		// Cities
		if ( ! empty( $redirect['cities'] ) && ! geot_target_city( $redirect['cities'] ) ) {
			return false;
		}

		// States
		if ( ! empty( $redirect['states'] ) && ! geot_target_state( $redirect['states'] ) ) {
			return false;
		}

		return true;
	}


	function count_click($field, $post_id, $dest_key = '') {

		$opts = geol_options( $post_id );

		switch($field) {
			case 'click' :
					if( isset( $opts['count_click'] ) && is_numeric( $opts['count_click'] ) )
						$opts['count_click']++;
					else
						$opts['count_click'] = 1;

					break;
			case 'match' :
					if( isset( $opts['count_match'] ) && is_numeric( $opts['count_match'] ) )
						$opts['count_match']++;
					else
						$opts['count_match'] = 1;

					break;
			case 'dest' :
					if( isset( $opts['dest'][$dest_key]['count_dest'] ) &&
						is_numeric( $opts['dest'][$dest_key]['count_dest'] )
					)
						$opts['dest'][$dest_key]['count_dest']++;
					else
						$opts['dest'][$dest_key]['count_dest'] = 1;

					break;
		}

		// save box settings
		update_post_meta( $post_id, 'geol_options', apply_filters( 'geol/redirect/count_click', $opts ) );

	}


	/**
	 * Add Shortcode
	 *
	 * @param
	 *
	 * @since 1.0.0
	 */
	public function add_shortcode($atts, $content = '') {
		$atts = shortcode_atts( array(
			'slug'			=> 'geo-slug',
			'nofollow'		=> 'no',
			'noreferrer'	=> 'no',
		), $atts, 'geo-link' );

		$return = '';
		$post = get_page_by_path($atts['slug'], OBJECT, 'geol_cpt');

		if( isset($post->ID) ) {

			$rel = array();
			$content = !empty($content) ? $content : 'Geo Link';
			$settings = geol_settings();
			$opts = geol_options($post->ID);


			// REL
			if( $atts['nofollow'] == 'yes' )
				$rel[] = 'nofollow';

			if( $atts['noreferrer'] == 'yes' )
				$rel[] = 'noreferrer';

			$attr_rel = count($rel) > 0 ? 'rel="'.implode(' ', $rel).'"' : '';


			// Output
			$link =  trailingslashit( site_url( $settings['goto_page'] ) ) . $opts['source_slug'];

			$return = '<a href="' . $link . '" '.$attr_rel.' >' . $content . '</a>';
		}

		return $return;
	}
}