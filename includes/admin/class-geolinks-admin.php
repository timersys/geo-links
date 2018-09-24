<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    GeoLinks
 * @subpackage GeoLinks/admin
 */
use GeotFunctions\GeotUpdates;

/**
 * @subpackage GeoLinks/admin
 * @author     Damian Logghe <damian@timersys.com>
 */
class GeoLinks_Admin {



	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ) {
		add_filter( 'plugin_action_links_' . GEOL_PLUGIN_HOOK, [$this, 'add_action_links'] );
		add_action( 'admin_init' , [ $this, 'handle_updates'], 0 );
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow, $post;

		/*if ( get_post_type() !== 'geotr_cpt' || !in_array( $pagenow, array( 'post-new.php', 'edit.php', 'post.php' ) ) )
			return;

		$post_id = isset( $post->ID ) ? $post->ID : '';

		wp_enqueue_script( 'geotr-admin-js', plugin_dir_url( __FILE__ ) . 'js/geotr-admin.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_style( 'geotr-admin-css', plugin_dir_url( __FILE__ ) . 'css/geotr-admin.css', array(), $this->version, 'all' );

		wp_localize_script( 'geotr-admin-js', 'geotr_js',
				array(
					'admin_url' => admin_url( ),
					'nonce' 	=> wp_create_nonce( 'geotr_nonce' ),
					'l10n'		=> array (
							'or'	=> '<span>'.__('OR', 'geotr' ).'</span>'
						),
					'opts'      => GeoLinks_Helper::get_options($post_id)
				)
		);*/
	}


	/**
	 * Register direct access link
	 *
	 * @since    1.0.0
	 * @return 	Array
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'edit.php?post_type=geol_cpt' ) . '">' . __( 'Create GeoLink', 'geotr' ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Handle Licences and updates
	 * @since 1.0.0
	 */
	public function handle_updates(){
		$opts = geot_settings();
		// Setup the updater
		return new GeotUpdates( GEOL_PLUGIN_FILE, [
				'version'   => GEOL_VERSION,
				'license'   => isset($opts['license']) ? $opts['license'] : ''
			]
		);
	}

}
