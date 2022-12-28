<?php
use admin\database\Database;
/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    GlitterCape
 * @subpackage GlitterCape/includes
 */

class Cape_Activator {

    private $db;

    public function __construct(){
        
        $this->db = Database::get_cape_db_instance();
        $this->initialize_table();

    }

    public function initialize_table(){

        $check_table_exists = $this->db->check_cape_table_exists();
        if( $check_table_exists === NULL || $check_table_exists === '' ){

            $this->db->cape_activation_table_initializer();
            return true;
        
        }
        else{

            return false;
        }
    }

}
