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
			$redirect_id = get_the_id();
			$opts        = geol_options( $redirect_id );
			$settings    = geol_settings();
			// check redirections to see if we have any match
			foreach ( $opts['dest'] as $redirect ) {
				// add default redirect code
				$redirect['status_code'] = $settings['redirect_code'];

				$redirect = apply_filters( 'geol/redirect_params', $redirect, $redirect_id );

				// validate redirect
				if ( $this->validate_redirection( $redirect ) ) {
					// last change to abort
					if ( apply_filters( 'geol/cancel_redirect', false, $redirect, $redirect_id ) ) {
						return;
					}
					wp_redirect( esc_url( $redirect['url'] ), $redirect['status_code'] );
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
	private function validate_redirection( $redirect, $geo ) {

		$referrer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';

		// Ref
		if ( ! empty( $dest['ref'] ) && strpos( $referrer, $redirect['ref'] ) === false ) {
			return false;
		}

		//Devices
		if ( $redirect['device'] == 'mobiles' && self::$detect->isMobile() ) {
			return false;
		}

		if ( $redirect['device'] == 'tablets' && self::$detect->isTablet() ) {
			return false;
		}

		if ( $redirect['device'] == 'desktop' && ( ! self::$detect->isTablet() && ! self::$detect->isMobile() ) ) {
			return false;
		}

		// Country
		if ( ! empty( $redirect['country'] ) && ! geot_target( $redirect['country'] ) ) {
			return false;
		}

		// regions
		if ( ! empty( $redirect['country_regions'] ) && ! geot_target( '', $redirect['country_regions'] ) ) {
			return false;
		}

		// Cities
		if ( ! empty( $redirect['city'] ) && ! geot_target_city( $redirect['city'] ) ) {
			return false;
		}

		// States
		if ( ! empty( $redirect['states'] ) && ! geot_target_state( $redirect['states'] ) ) {
			return false;
		}

		return true;
	}
}