<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://geotargetingwp.com/geo-links
 * @since      1.0.0
 *
 * @package    GeoLinks
 * @subpackage GeoLinks/includes
 */
use GeotFunctions\Setting\GeotSettings;


/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    GeoLinks
 * @subpackage GeoLinks/includes
 * @author     Your Name <email@example.com>
 */
class GeoLinks {

	/**
	 * @var GeoLinks_Public $public
	 */
	public $public;

	/**
	 * @var GeoLinks_Admin $admin
	 */
	public $admin;
	/**
	 * @var mixed|void Geotarget settings
	 */
	public $opts;
	public $geot_opts;


	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * Plugin Instance
	 * @since 1.0.0
	 * @var The Geot plugin instance
	 */
	protected static $_instance = null;

	/**
	 * Main Geot Instance
	 *
	 * Ensures only one instance of WSI is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see GEOT()
	 * @return GeoLinks
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 * @param mixed $key
	 * @since 1.0.0
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'payment_gateways', 'shipping', 'mailer', 'checkout' ) ) ) {
			return $this->$key();
		}
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'geol';
		$this->version = GEOL_VERSION;

		$this->load_dependencies();
		GeotSettings::init();

		//$this->opts = geot_settings();
		//$this->geol_opts = geot_pro_settings();
		$this->set_locale();
		$this->define_public_hooks();
		$this->define_global_hooks();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-geolinks-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/global/GeoLinksCpt.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/admin/class-geolinks-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/admin/class-geolinks-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/public/class-geolinks-public.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the GeoLinks_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new GeoLinks_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		add_action( 'plugins_loaded', [$plugin_i18n, 'load_plugin_textdomain'] );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$this->admin = new GeoLinks_Admin();
		$this->settings = new GeoLinks_Settings();

		add_filter( 'plugin_action_links_' . GEOL_PLUGIN_HOOK, [ $this->admin, 'add_action_links' ] );

		// settings page
		add_action( 'admin_menu', [ $this->settings, 'add_settings_menu' ]);
		add_action( 'admin_init', [ $this->settings, 'save_settings' ]);

		// License and Updates
		add_action( 'admin_init' , [ $this->admin, 'handle_updates' ], 0 );

		add_action( 'admin_enqueue_scripts', [ $this->admin, 'enqueue_scripts' ] );
	}


	/**
	 * Register all of the hooks that run globally
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_global_hooks() {
		$this->global = new GeoLinksCpt();

		//CPT
		add_action( 'init', [ $this->global, 'register_cpt' ] );
		add_action( 'add_meta_boxes_geol_cpt', [ $this->global, 'add_meta_boxes' ] );
		add_action( 'save_post_geol_cpt', [ $this->global, 'save_meta_options' ] );
		add_filter( 'manage_geol_cpt_posts_columns', [ $this->global, 'set_custom_cpt_columns' ] );
		add_action( 'manage_geol_cpt_posts_custom_column', [ $this->global, 'set_custom_cpt_values' ],10, 2);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->public = new GeoLinks_Public();

		add_action( 'init', [ $this->public , 'add_endpoint' ] );
		add_filter( 'request', [ $this->public , 'endpoint_404' ] );
		add_action( 'template_redirect', [ $this->public , 'endpoint_redirect' ] );
	}


	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}
}
