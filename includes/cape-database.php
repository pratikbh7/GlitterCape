<?php
namespace admin\database;

/**
 * Database handlers.
 *
 * @link      
 * @since      1.0.0
 *
 * @package    GlitterCape
 * @subpackage GlitterCape/admin
 */

 class Database{

    private static $instance = null;
    
    private $db;

    private $db_prefix;

    private $db_charset_collate;

    private $primary_table;

    private $tables = array( 'init_cape_slides_table' => 
    "(`id`                  INT(11)      NOT NULL AUTO_INCREMENT,
      `Slider_name`       VARCHAR(128) NOT NULL,
      `Template_number`     VARCHAR(50)  NOT NULL,
      `Slide_category`      VARCHAR(128)  NOT NULL,
      `Slide_selection`       VARCHAR(128) NOT NULL,
      `editable`            INT(11)      NOT NULL DEFAULT '1',
      PRIMARY KEY (`id`),
      INDEX cape_template (`Slider_name`, `Template_number`, `Slide_selection`)
    )" );
    
    private function __construct(){

        global $wpdb;
        $this->db = $wpdb;
        $this->db_prefix = $wpdb->prefix;
        $this->db_charset_collate = $wpdb->get_charset_collate();
        $this->primary_table = $this->db_prefix . 'glitter_slides';

    }

    public function cape_activation_table_initializer(){

        $table = $this->tables[ 'init_cape_slides_table' ];
        $init_query = 'CREATE TABLE IF NOT EXISTS `' . $this->primary_table . '` ' . $table;
        $init_query .= ' ' . $this->db_charset_collate;
        $prepare_init_query = $this->db->prepare( $init_query );
        $run_init_query = $this->execute_query( $prepare_init_query );

        if( $run_init_query === false){
            $this->db_error_handle();
        }

    }

    public function insert_user_data( $data ){

        $format = array( '%s' , '%s', '%s', '%s', '%d' );
        $data_to_insert = array( 'Slider_name' => $data[ 'slider_name' ], 
                                 'Template_number' => $data[ 'selected_template' ],
                                 'Slide_category' => $data[ 'selected_category' ], 
                                 'Slide_selection' => $data[ 'selected_slides' ], 
                                 'editable' => 1 );
        $run_insert_query = $this->db->insert( $this->primary_table, $data_to_insert, $format );

        if( $run_insert_query === false ){
            $this->db_error_handle();
        }
        else{
            return true;
        }

    }

    public function execute_query( $query ){

        return $this->db->query( $query );

    }

    public function db_error_handle( ){

        $this->db->show_errors(true);
        $this->db->print_error();
        exit();

    }

    public function get_slider_config_data(){

        $retrieve_query = 'SELECT * FROM `' . $this->primary_table . '` LIMIT 1';
        $prepare_retrieve_query = $this->db->prepare( $retrieve_query );
        $run_retrieve_query = $this->db->get_row( $prepare_retrieve_query, ARRAY_N );
        if( $run_retrieve_query === false){

            $this->db_error_handle();

        }
        else{
            $run_retrieve_query = array_slice( $run_retrieve_query, 3, 2 );         
            return $run_retrieve_query;
        } 

    }

    public function check_cape_table_exists(){

        $check_query = 'SHOW TABLES LIKE %s';
        $prepare_check_query = $this->db->prepare( $check_query, $this->db->esc_like( $this->primary_table) );
        return $this->db->get_var( $prepare_check_query ); 

    }
    //singleton
    public static function get_cape_db_instance(){

        if(!self::$instance){
            self::$instance = new Database();
        }
        return self::$instance;
    }

 } 