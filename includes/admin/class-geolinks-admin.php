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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
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
	}

	/**
	* Add menu for Settings page of the plugin
	* @since  1.0.3
	* @return  void
	*/
	public function add_settings_menu() {

		add_submenu_page( 'geot-settings', 'Geo Link Settings', 'Geo Link Settings', apply_filters( 'geol/settings_page_role', 'manage_options'), 'geol-settings',array($this, 'settings_page') );
	}

	/**
	* Settings page for plugin
	* @since 1.0.3
	*/
	public function settings_page() {

		$opts = geol_settings();
		$pages = get_pages( array( 'post_status' => 'publish' ) );
		$domain = $this->plugin_name;

		include GEOL_PLUGIN_DIR  . 'includes/admin/views/settings-page.php';
	}

	/**
	* Save Settings page
	* @since 1.0.3
	*/
	public function save_settings(){
		if (isset( $_POST['geol_settings'] ) &&
			isset( $_POST['geol_nonce'] ) &&
			wp_verify_nonce( $_POST['geol_nonce'], 'geol_save_settings' )
		) {

			$_POST['geol_settings']['goto_url'] = trailingslashit( site_url( $_POST['geol_settings']['goto_page'] ) );

			$settings = esc_sql( $_POST['geol_settings'] );

			update_option( 'geol_settings' ,  $settings);
		}
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

	/**
	* Register the metaboxes for our cpt
	* @since    1.0.0
	* @return   void
	*/
	public function add_meta_boxes() {
		add_meta_box(
			'geol-opts',
			 __( 'Redirection Options', $this->plugin_name ),
			array( $this, 'geol_opts' ),
			'geol_cpt',
			'normal',
			'core'
		);
	}

	/**
	* Include the metabox view for opts
	* @param  object $post    geotrcpt post object
	* @param  array $metabox full metabox items array
	* @since 1.0.0
	*/
	public function geol_opts( $post, $metabox ) {

		$settings = geol_settings();
		$opts = Geol_Helper::get_options( $post->ID );
		$devices = Geol_Helper::get_devices();

		$domain = $this->plugin_name;

		include GEOL_PLUGIN_DIR . '/includes/admin/views/metaboxes-opts.php';
	}


	/**
	* Saves the post meta of redirections
	* @since 1.0.0
	*/
	function save_meta_options( $post_id ){

		// Verify that the nonce is set and valid.
		if ( !isset( $_POST['geol_options_nonce'] ) || ! wp_verify_nonce( $_POST['geol_options_nonce'], 'geol_options' ) ) {
			return $post_id;
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// same for ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $post_id;
		}
		// same for cron
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return $post_id;
		}
		// same for posts revisions
		if ( is_int( wp_is_post_autosave( $post_id ) ) ) {
			return $post_id;
		}

		// can user edit this post?
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$opts = $_POST['geol'];
		unset( $_POST['geol'] );

		$post = get_post($post_id);
		$settings = geol_settings();

		// sanitize settings
		$input['source_slug'] = sanitize_text_field($opts['source_slug']);
		
		if( is_array($opts['dest']) && count($opts['dest']) > 0 ) { $i = 0;
			foreach($opts['dest'] as $data) {
				$key = 'dest_'.$i;
				$input['dest'][$key]['url']		= esc_url($data['url']);
				$input['dest'][$key]['country']	= esc_html($data['country']);
				$input['dest'][$key]['state']	= esc_html($data['state']);
				$input['dest'][$key]['city']	= esc_html($data['city']);
				$input['dest'][$key]['device']	= esc_html($data['device']);
				$input['dest'][$key]['ref']		= esc_url($data['ref']);
				$i++;
			}
		}

		// save box settings
		update_post_meta( $post_id, 'geol_options', apply_filters( 'geol/metaboxes/sanitized_options', $input ) );
	}



	function add_name_column($columns) {

		foreach($columns as $key_column => $value_column) {
			
			$ok_columns[$key_column] = $value_column;

			if( $key_column == 'title' ) {
				$ok_columns['source_url'] = __( 'Source URL', $this->plugin_name );
				$ok_columns['count_dest'] = __( 'Num Destinations', $this->plugin_name );
			}
		}
		
		return apply_filters('geol/manage_columns/name',$ok_columns,$columns);
	}


	function add_value_column($column, $post_id) {

		$settings = geol_settings();
		$opts = Geol_Helper::get_options( $post_id );

		switch($column) {
			case 'source_url' : $value_column = $settings['goto_url'].$opts['source_slug']; break;
			case 'count_dest' : $value_column = count($opts['dest']); break;
		}

		echo apply_filters('geol/manage_columns/value', $value_column, $column, $post_id);
	}
}
?>