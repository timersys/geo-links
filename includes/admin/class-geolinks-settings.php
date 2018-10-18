<?php

class GeoLinks_Settings {

	public function __construct() {

		add_action( 'admin_menu', [ $this, 'add_settings_menu' ] );
		add_action( 'admin_init', [ $this, 'save_settings' ] );
	}

	/**
	 * Add menu for Settings page of the plugin
	 * @since  1.0.0
	 * @return  void
	 */
	public function add_settings_menu() {

		add_submenu_page( 'geot-settings', 'GeoLinks Settings', 'GeoLinks Settings', apply_filters( 'geol/settings_page_role', 'manage_options' ), 'geol-settings', [
			$this,
			'settings_page',
		] );
	}

	/**
	 * Settings page for plugin
	 * @since 1.0.3
	 */
	public function settings_page() {
		$opts = geol_settings();

		include GEOL_PLUGIN_DIR . 'includes/admin/settings/settings-page.php';
	}

	/**
	 * Save Settings page
	 * @since 1.0.3
	 */
	public function save_settings() {
		if ( isset( $_POST['geol_settings'] ) &&
		     isset( $_POST['geol_nonce'] ) &&
		     wp_verify_nonce( $_POST['geol_nonce'], 'geol_save_settings' )
		) {

			$settings = esc_sql( $_POST['geol_settings'] );

			update_option( 'geol_settings', $settings );

			GeoLinks_Permalinks::set_flush_needed();

			wp_redirect(admin_url('admin.php?page=geol-settings'));
			exit();
		}
	}
}