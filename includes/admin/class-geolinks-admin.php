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
	* The version of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $version    The current version of this plugin.
	*/
	private $version;

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	*/
	public function __construct() {

		$this->version = GEOL_VERSION;

		add_filter( 'plugin_action_links_' . GEOL_PLUGIN_HOOK, [ $this, 'add_action_links' ] );

		// License and Updates
		add_action( 'admin_init' , [ $this, 'handle_updates' ], 0 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}


	/**
	* Register the JavaScript for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {
		global $pagenow, $post;

		if ( get_post_type() !== 'geol_cpt' || !in_array( $pagenow, array( 'post-new.php', 'edit.php', 'post.php' ) ) )
			return;

		wp_enqueue_script( 'geol-admin-js', plugin_dir_url( __FILE__ ) . 'js/geol-admin.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_style( 'geol-admin-css', GEOL_PLUGIN_URL . 'includes/admin/css/geol-admin.css', array(), $this->version, 'all' );

		wp_localize_script( 'geol-admin-js', 'geol_var',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'geol_nonce' )
				)
		);

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
				'version'   => $this->version,
				'license'   => isset($opts['license']) ? $opts['license'] : ''
			]
		);
	}
}