<?php
use admin\database\Database;
/**
 * Define and pass content to shortcode
 *
 * @since      1.0.0
 *
 * @package    GlitterCape
 * @subpackage GlitterCape/includes
 */

class Cape_Shortcode{

    public function __construct(){

        add_shortcode( 'cape_slider', array( $this, 'cape_shortcode_content' ) );

    }

    public function cape_shortcode_content(){

        $db = Database::get_cape_db_instance();
        $slide_data = $db->get_slider_config_data();
        ob_start();
        include( CAPE_PATH . '/public/partials/cape-front-display.php');
        $cape_content = ob_get_contents();
        ob_end_clean();
        return $cape_content;

    }
}
