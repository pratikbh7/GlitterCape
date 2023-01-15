<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    GlitterCape
 * @subpackage GlitterCape/includes
 */

class Cape_Core {

	protected $loader;

	protected $plugin_name;

	protected $version;

	public function __construct() {

		$this->version = CAPE_VERSION;
		$this->plugin_name = 'GlitterCape';

		$this->load_dependencies();
		// $this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {

		require_once CAPE_PATH . 'includes/cape-loader.php';

		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cape-i18n.php';

		require_once CAPE_PATH . 'includes/cape-database.php';
		
		require_once CAPE_PATH . 'admin/cape-admin-func.php';

		require_once CAPE_PATH . 'public/cape-public-func.php';

		require_once CAPE_PATH . 'includes/cape-shortcode.php';

		$this->content = new Cape_Shortcode();

		$this->loader = new Cape_Loader();

	}

	private function set_locale() {

		$plugin_i18n = new Plugin_Name_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_admin_hooks() {

		$cape_admin = new Cape_Admin( $this->get_plugin_name(), $this->get_version() );

		// $this->loader->add_action( 'admin_notices', $cape_admin, 'cape_admin_notices' ); configuration notice outside plugin admin page
		$this->loader->add_action( 'init', $cape_admin, 'register_cape_taxonomy' );
		$this->loader->add_action( 'init', $cape_admin, 'register_cape_post_type' );
		$this->loader->add_action( 'init', $cape_admin,'share_namespace_with_cape' );
		$this->loader->add_action( 'admin_enqueue_scripts', $cape_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $cape_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $cape_admin, 'add_admin_menu_page' );
		$this->loader->add_action( 'admin_post_cape_form_response', $cape_admin, 'cape_form_response' );
		$this->loader->add_action( 'wp_ajax_slide_cat_selection', $cape_admin, 'slide_cat_selection' );
		
	}

	private function define_public_hooks() {

		$cape_public = new Cape_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $this->content, 'add_cape_shortcode' );
		$this->loader->add_action( 'wp_enqueue_scripts', $cape_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $cape_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'script_loader_tag', $cape_public, 'add_type_script_attribute', 10, 3 );

	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
