<?php

/**
 * Class GeoLinksCpt will handle all stuff related to custom post type
 * @since 1.0.0
 */
class GeoLinksCpt {

	public function __construct() {
		$this->register_cpt();
		//$this->set_custom_cpt_columns();
	}

	/**
	 * Register custom post types
	 * @since     1.0.0
	 * @return void
	 */
	private function register_cpt() {

		$labels = array(
			'name'               => 'Geo Links v'.GEOL_VERSION,
			'singular_name'      => _x( 'Geo Links', 'post type singular name', 'popups' ),
			'menu_name'          => _x( 'Geo Links', 'admin menu', 'popups' ),
			'name_admin_bar'     => _x( 'Geo Links', 'add new on admin bar', 'popups' ),
			'add_new'            => _x( 'Add New', 'Geo Redirection', 'popups' ),
			'add_new_item'       => __( 'Add New Geo Redirection', 'popups' ),
			'new_item'           => __( 'New Geo Redirection', 'popups' ),
			'edit_item'          => __( 'Edit Geo Redirection', 'popups' ),
			'view_item'          => __( 'View Geo Redirection', 'popups' ),
			'all_items'          => __( 'Geo Links', 'popups' ),
			'search_items'       => __( 'Search Geo Redirection', 'popups' ),
			'parent_item_colon'  => __( 'Parent Geo Redirection:', 'popups' ),
			'not_found'          => __( 'No Geo Redirection found.', 'popups' ),
			'not_found_in_trash' => __( 'No Geo Redirection found in Trash.', 'popups' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'geot-settings',
			'query_var'          => true,
			'exclude_from_search'=> true,
			'rewrite'            => array( 'slug' => 'geol_cpt' ),
			'capability_type'    => 'post',
			'capabilities' => array(
				'publish_posts' 		=> apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'edit_posts' 			=> apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'edit_others_posts' 	=> apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'delete_posts' 			=> apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'delete_others_posts' 	=> apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'read_private_posts' 	=> apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'edit_post' 			=> apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'delete_post' 			=> apply_filters( 'geol/settings_page/roles', 'manage_options'),
				'read_post' 			=> apply_filters( 'geol/settings_page/roles', 'manage_options'),
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
	public function set_custom_cpt_columns( $columns ){
		$new_column = [];

		foreach ($columns as $key => $value ){
			if( $key == 'date')
				$new_column['url']        = __( 'Destination URL', 'geotr' );
			$new_column[$key] = $value;
		}

		return $new_column;
	}
}

new GeoLinksCpt();