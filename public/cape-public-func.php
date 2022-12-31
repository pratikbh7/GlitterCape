<?php
use admin\database\Database;
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    GlitterCape
 * @subpackage GlitterCape/public
 */


class Cape_Public {

	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	//enqueue styles
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name . 'styles', CAPE_PUBLIC_ASSETS . 'css/cape-front.css', array(), $this->version, 'all' );
	}

	//enqueue scripts
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name . 'Script', CAPE_PUBLIC_ASSETS . 'js/cape-front.js', array( 'jquery' ), $this->version, false );
	}

	//add "module" to type attribute of script
	public function add_type_script_attribute( $tag, $handle, $src ){

		if (  $handle === 'GlitterCapeScript' ) {
			$tag = "<script type='module' src='" . esc_url( $src ) . "'></script>";
			return $tag;
		}
		return $tag;

	}  

}
