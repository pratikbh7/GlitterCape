<?php
defined('ABSPATH') or exit;

/**
 * Plugin Name:       GlitterCape
 * Description:       Slider animations for your slides
 * Version:           1.0.0
 * Author:            Pratik Bhetwal
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-name
 * Domain Path:       /languages
 */

defined( 'CAPE_PATH' ) or define( 'CAPE_PATH', plugin_dir_path( __FILE__ ) );
defined( 'CAPE_URL' ) or define( 'CAPE_URL', plugin_dir_url( __FILE__ ) );
defined( 'CAPE_PUBLIC_ASSETS' ) or define( 'CAPE_PUBLIC_ASSETS', CAPE_URL . 'public/' );
defined( 'CAPE_ADMIN_ASSETS' ) or define( 'CAPE_ADMIN_ASSETS', CAPE_URL . 'admin/' );
defined( 'CAPE_VERSION') or define( 'CAPE_VERSION', '1.0.0' );

function activate_plugin_name() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/cape-activator.php';
    $activate = new Cape_Activator();
}

// function deactivate_plugin_name() {
//     require_once plugin_dir_path( __FILE__ ) . 'includes/cape-deactivator.php';
//     Plugin_Name_Deactivator::deactivate();
// }

register_activation_hook( __FILE__, 'activate_plugin_name' );
// // register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );
    
require CAPE_PATH . 'includes/cape-core.php';

function run_cape() {

    $plugin = new Cape_Core();
    $plugin->run();

}
run_cape();

