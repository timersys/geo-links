<?php
class GeoLinks_Notices{
	/**
	 * GeoLinks_Notices constructor.
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this , 'admin_notices'] );
		add_action( 'wp_ajax_dismiss_geot_notice', [ $this, 'dismiss_notices'] );
	}

	/**
	 * Dismiss notices captured in ajax
	 */
	public function dismiss_notices(){
		if( !isset($_GET['notice']) ) {
			return;
		}
		update_option($_GET['notice'], true);
		die();
	}

	/**
	 * Show notice if cache plugin exists
	 */
	public function admin_notices() {
		if( \GeotFunctions\is_caching_plugin_active() && ! get_option('geolinks-cache')) {
			?>
			<div class="geot-notice notice notice-error is-dismissible" data-notice-id="geolinks-cache">
				<h3><i class=" dashicons-before dashicons-admin-site"></i> GeoLinks</h3>
				<p>We detected that your have a cache plugin active.</p>
				<p>Please be sure to whitelist the geol_cpt custom post type in your cache plugin.</p>
			</div>
			<script>
                jQuery(document).on('click', '.geot-notice .notice-dismiss', function() {
                    var notice_id = jQuery(this).parent('.geot-notice').data('notice-id');
                    jQuery.ajax({
                        url: ajaxurl,
                        data: {
                            action: 'dismiss_geot_notice',
	                        notice: notice_id,
                        }
                    })

                })
			</script>
			<?php
		}
	}
}
new GeoLinks_Notices();