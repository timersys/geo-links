<?php

/**
 * Class GeoLinks_AJAX will handle all admin ajax calls
 * @since 1.0.0
 */
class Geolinks_Ajax {
	public function __construct() {
		add_action( 'wp_ajax_geol_source', [ $this, 'validate_source' ] );
	}

	/**
	 * Validate source field when adding new links
	 * @since 1.0.0
	 */
	function validate_source() {

		$ouput = [];

		if ( ! isset( $_POST['slug'] ) || ! isset( $_POST['wpnonce'] ) ||
		     ! wp_verify_nonce( $_POST['wpnonce'], 'geol_nonce' )
		) {
			wp_send_json( $ouput );
		}

		global $wpdb;

		$output = [
			'type' => 'success',
			'msg'  => __( 'Source available.' ),
			'icon' => 'dashicons-yes',
		];

		$source_slug	= sanitize_title( $_POST['slug'] );
		$exclude_id		= sanitize_title( $_POST['exclude'] );
		$meta_key		= 'geol_options';

		if( isset($exclude_id) && is_numeric($exclude_id) ) {

			$query 		= 'SELECT
								meta_value
							FROM
								' . $wpdb->postmeta . '
							WHERE
								post_id <> %d && meta_key = %s';

			$results 	= $wpdb->get_results( $wpdb->prepare( $query, $exclude_id, $meta_key ) );
		} else {
			$query 		= 'SELECT meta_value FROM ' . $wpdb->postmeta . ' WHERE meta_key = %s';
			$results 	= $wpdb->get_results( $wpdb->prepare( $query, $meta_key ) );
		}

		foreach ( $results as $result ) {

			$opts = maybe_unserialize( $result->meta_value );

			if ( isset( $opts['source_slug'] ) && $opts['source_slug'] == $source_slug ) {

				$output = [
					'type' => 'error',
					'msg'  => __( 'Source in use. Please choose other source' ),
					'icon' => 'dashicons-no',
				];
				break;
			}
		}

		wp_send_json( $output );
	}
}
new Geolinks_Ajax();