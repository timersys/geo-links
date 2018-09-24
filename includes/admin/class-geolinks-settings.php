<?php

class GeoLinks_Settings {

	public function __construct() {

		add_action( 'admin_menu' , [ $this, 'add_settings_menu' ],8);
		add_action( 'admin_init', [ $this, 'save_settings' ] );

	}

	/**
	 * Add menu for Settings page of the plugin
	 * @since  1.0.0
	 * @return  void
	 */
	public function add_settings_menu() {

		add_submenu_page( 'geot-settings', 'GeoLinks Settings', 'GeoLinks Settings', apply_filters( 'geol/settings_page_role', 'manage_options'), 'geotr-settings', [$this, 'settings_page'] );
	}

	/**
	 * Settings page for plugin
	 * @since 1.0.3
	 */
	public function settings_page() {
		$defaults = [
			'ajax_mode'                 => '0',
		];
		$opts = wp_parse_args( geol_settings(),  $defaults );
		include  dirname( __FILE__ )  . '/partials/settings-page.php';
	}

	/**
	 * Save Settings page
	 * @since 1.0.3
	 */
	function save_settings(){
		if (  isset( $_POST['geot_nonce'] ) && wp_verify_nonce( $_POST['geot_nonce'], 'geol_save_settings' ) ) {
			$settings = isset($_POST['geol_settings']) ? esc_sql( $_POST['geol_settings'] ) : '';

			update_option( 'geol_settings' ,  $settings);
		}
	}
}