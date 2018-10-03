<?php

/**
 * Class GeoLinksCpt will handle all stuff related to custom post type
 * @since 1.0.0
 */
class GeoLinksCpt {

	public function __construct() {
		add_action( 'init', [ $this, 'register_cpt' ] );
		add_action( 'add_meta_boxes_geol_cpt', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post_geol_cpt', [ $this, 'save_meta_options' ] );
		add_filter( 'manage_geol_cpt_posts_columns', [ $this, 'set_custom_cpt_columns' ] );
		add_action( 'manage_geol_cpt_posts_custom_column', [ $this, 'set_custom_cpt_values' ],10, 2);
		add_filter( 'wp_insert_post_data', [ $this, 'modify_post_name' ], 10, 2);

		add_action( 'wp_ajax_geol_source', array($this,'validate_source'));
		add_action( 'wp_ajax_nopriv_geol_source', array($this,'validate_source'));
	}

	/**
	 * Register custom post types
	 * @since     1.0.0
	 * @return void
	 */
	public function register_cpt() {

		$settings = geol_settings();

		$labels = array(
			'name'               => 'Geo Links v'.GEOL_VERSION,
			'singular_name'      => _x( 'Geo Links', 'post type singular name', 'popups' ),
			'menu_name'          => _x( 'Geo Links', 'admin menu', 'popups' ),
			'name_admin_bar'     => _x( 'Geo Links', 'add new on admin bar', 'popups' ),
			'add_new'            => _x( 'Add New', 'Geo Links', 'popups' ),
			'add_new_item'       => __( 'Add New Geo Links', 'popups' ),
			'new_item'           => __( 'New Geo Links', 'popups' ),
			'edit_item'          => __( 'Edit Geo Links', 'popups' ),
			'view_item'          => __( 'View Geo Links', 'popups' ),
			'all_items'          => __( 'Geo Links', 'popups' ),
			'search_items'       => __( 'Search Geo Links', 'popups' ),
			'parent_item_colon'  => __( 'Parent Geo Links:', 'popups' ),
			'not_found'          => __( 'No Geo Links found.', 'popups' ),
			'not_found_in_trash' => __( 'No Geo Links found in Trash.', 'popups' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'geot-settings',
			'query_var'          => true,
			'exclude_from_search'=> true,
			'rewrite'            => array( 'slug' => $settings['goto_page'] ),
			'capability_type'    => 'post',
			'capabilities' => array(
				'publish_posts'         => apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'edit_posts'            => apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'edit_others_posts'     => apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'delete_posts'          => apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'delete_others_posts'   => apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'read_private_posts'    => apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'edit_post'             => apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'delete_post'           => apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'read_post'             => apply_filters( 'geol/settings_page/roles', 'manage_options'),
			),
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 10,
			'supports'           => array( 'title' )
		);

		register_post_type( 'geol_cpt', $args );
	}


	/**
	 * Add custom columns to cpt
	 *
	 * @param [type] $columns [description]
	 *
	 * @since  1.2
	 * @return mixed
	 */
	function set_custom_cpt_columns($columns) {

		foreach($columns as $key => $value) {
			
			$new_column[$key] = $value;

			if( $key == 'title' ) {
				$new_column['source_url'] = __( 'Destination URL', 'geol' );
				$new_column['count_dest'] = __( 'Destination Num', 'geol' );
			}
		}
		
		return apply_filters('geol/manage_columns/name',$new_column,$columns);
	}


	/**
	 * Add custom values columns to cpt
	 *
	 * @param [type] $columns [description]
	 *
	 * @since  1.2
	 * @return mixed
	 */
	function set_custom_cpt_values($column, $post_id) {

		$settings = geol_settings();
		$opts = geol_options( $post_id );

		switch($column) {
			case 'source_url' : $value_column = $settings['goto_url'].$opts['source_slug']; break;
			case 'count_dest' : $value_column = count($opts['dest']); break;
		}

		echo apply_filters('geol/manage_columns/value', $value_column, $column, $post_id);
	}


	/**
	 * Register the metaboxes for our cpt
	 * @since    1.0.0
	 * @return   void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'geol-opts',
			 __( 'Redirection Options', 'geol' ),
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
		$opts = geol_options( $post->ID );
		$devices = geol_devices();

		include GEOL_PLUGIN_DIR . '/includes/admin/metaboxes/metaboxes-opts.php';
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

		if( isset($post->post_name) ) {
			$source_slug = sanitize_title($opts['source_slug']);
			$input['source_slug'] = $post->post_name == $source_slug ? $source_slug : $post->post_name;
		} else
			$input['source_slug'] = sanitize_title($opts['source_slug']);
		
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

	/**
	 * Modify post_name
	 * @since 1.0.0
	 */
	public function modify_post_name($data, $postarr) {

		if ( !isset( $postarr['geol_options_nonce'] ) ||
			 !wp_verify_nonce( $postarr['geol_options_nonce'], 'geol_options' ) ||
			 $postarr['post_type'] != 'geol_cpt' ||
			 $postarr['post_status'] != 'publish' ||
			 $postarr['post_parent'] != 0
			) return $data;

		$post_id = isset( $postarr['ID'] ) && is_numeric( $postarr['ID'] ) ? $postarr['ID'] : 0;

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $data;
		}
		// same for ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $data;
		}
		// same for cron
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return $data;
		}
		// same for posts revisions
		if ( is_int( wp_is_post_autosave( $post_id ) ) ) {
			return $data;
		}

		// can user edit this post?
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $data;
		}
		
		$post_type = $postarr['post_type'];
		$post_status = $postarr['post_status'];
		$post_parent = $postarr['post_parent'];
		$post_name = sanitize_title( $postarr['geol']['source_slug'] );

		$data['post_name'] = wp_unique_post_slug($post_name, $post_id, $post_status, $post_type, $post_parent );
		
		return $data;
	}


	/**
	 * validate source field
	 * @since 1.0.0
	 */
	function validate_source() {

		$ouput = array();

		if( !isset($_POST['slug']) || !isset( $_POST['wpnonce'] ) ||
			!wp_verify_nonce( $_POST['wpnonce'], 'geol_nonce' )
		) wp_send_json($ouput);

		global $wpdb;

		$output = array(
						'type' => 'success',
						'msg' => __('Source available.'),
						'icon' => 'dashicons-yes'
					);

		$source_slug = sanitize_title($_POST['slug']);
		$meta_key = 'geol_options';
		$query = 'SELECT post_id, meta_value FROM '.$wpdb->postmeta.' WHERE meta_key = %s';

		$results = $wpdb->get_results($wpdb->prepare($query, $meta_key));

		foreach( $results as $result ) {

			$opts = maybe_unserialize($result->meta_value);

			if( $opts['source_slug'] == $source_slug ) {
				
				$output = array(
								'type' => 'error',
								'msg' => __('Source in use. Please choose other source'),
								'icon' => 'dashicons-no'
							);
				break;
			}
		}

		wp_send_json($output);
	}
}

new GeoLinksCpt();