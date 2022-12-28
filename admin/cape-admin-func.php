<?php
use admin\database\Database;
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    GlitterCape
 * @subpackage GlitterCape/admin
 */
class Cape_Admin {

	private $plugin_name;

	private $version;

	private $options;

	private $parent_menu_slug;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->parent_menu_slug = "glitter_cape";

	}

	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name . 'styles', CAPE_ADMIN_ASSETS . 'css/cape-admin.css', array(), $this->version, 'all' );

	}

	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name . 'script', CAPE_ADMIN_ASSETS . 'js/cape-main.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name . 'script', 'slide_cat_selection', array( 'url' => esc_url( admin_url( 'admin-ajax.php' ) ) ) );

	}

	public function add_admin_menu_page(){

		add_menu_page( 
				__( 'Slider Configuration', 'textdomain' ), 
				__( 'Cape Slider', 'textdomain' ), 
				'manage_options', 
				$this->parent_menu_slug, 
				'',
				'dashicons-cape_admin_icon', 
				8 );

		add_submenu_page( 
				$this->parent_menu_slug,
				__( 'Slider Configuration', 'textdomain' ), 
				__( 'Slider Config', 'textdomain' ), 
				'manage_options',
				$this->parent_menu_slug,
				array($this, 'display_admin'),
				1 );

		add_submenu_page( 
				$this->parent_menu_slug,
				__( 'Guide', 'textdomain' ), 
				__( 'Guide', 'textdomain' ), 
				'manage_options',
				'guide',
				'',
				3 );

	
		// add_action('load-' . $hook_suffix, array( $this , 'cape_test') ); future usage for trigggering configuration notices when outside plugin admin page
		
	}

	// public function cape_test(){
	// 	remove_action( 'admin_notices', array( $this, 'cape_admin_notices' ) );
	// }

	// public function cape_admin_notices(){
	// 	echo "<div id='notice' class='updated fade'><p>Cape is not configured yet. Please do it now.</p></div>\n";
	// }

	public function display_admin( $message ) {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'Insufficient priviliges' ) );
		}
		$default_configs = array( 'template' => 'Template1',
								  'slide_name' => '');
		$options = get_option( 'cape_configs' );
		if( !$options ){
			$options = $default_configs;
		}
		?>
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<?php if( current_user_can( 'edit_users' ) ) { ?>
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="cape_admin_form">
				<?php
					require_once CAPE_PATH . 'admin/partials/cape-admin-display.php';
					submit_button( __( 'Save Settings', 'textdomain' ) );
				}
				else{
					?>
						<p> <?php __("Unauthorized form access", 'textdomain') ?> </p>
						<?php  
					}
				?>
			</form>
		<?php
	}

	public function cape_form_response() {

		if( isset( $_POST['cape_nonce'] ) && wp_verify_nonce( $_POST['cape_nonce'], 'cape_admin_nonce') ) {

			$cape_template = sanitize_text_field( $_POST['cape_admin']['template_select'] );
			$cape_slider_name = sanitize_text_field( $_POST['cape_admin']['slider_name'] );
			$cape_slide_cat = sanitize_text_field( $_POST['cape_admin']['category_select']);
			$cape_slides_array =  $_POST['cape_admin']['slides_selection'];
			$cape_slides = array_map('sanitize_text_field', $cape_slides_array );
			$cape_slides = implode( ",", $cape_slides );
			$cape_data = array( 'selected_template' => $cape_template,
								'slider_name'       => $cape_slider_name,
								'selected_category' => $cape_slide_cat,
								'selected_slides'   => $cape_slides
							);

			$db = Database::get_cape_db_instance();
			$insert_data = $db->insert_user_data( $cape_data );

			$admin_notice = "cape_success" . $insert_data;

			$this->custom_redirect( $admin_notice, $_POST['cape_admin'] );
			exit;
		}			
		else {
			wp_die( __( 'Invalid nonce', 'textdomain' ), __( 'Error', 'textdomain' ), array(
				'response' 	=> 403,
				'back_link' => 'admin.php?page=' . $this->parent_menu_slug ) );
		}

	}

	public function slide_cat_selection(){

		if( isset( $_POST['category']) && $_POST['category'] !== null ){

			$category = sanitize_text_field( $_POST['category'] );
			$post_args = array( 'post_type'   => 'cape_slide', 
								'numberposts' => -1, 
								'tax_query'   => array( 'taxonomy'        => 'cape_slide_categories',
													   'include_children' => true));
			$message = get_posts( $post_args );
			wp_send_json( $message );	
			wp_die();
			
		} 

	}

	public function custom_redirect( $admin_notice, $response ) {

		wp_redirect( esc_url_raw( add_query_arg( array(
									'cape_notice' => $admin_notice
									),
							admin_url('admin.php?page=' . $this->parent_menu_slug) 
					) ) );

	}

	public function share_namespace_with_cape(){

		register_taxonomy_for_object_type( 'cape_slide_categories', 'cape_slide' );

	}

	public function register_cape_taxonomy(){

		$labels = array(
			'name'              => _x( 'Slide Categories', 'taxonomy general name' ),
			'singular_name'     => _x( 'Slide Category', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Slide Categories' ),
			'all_items'         => __( 'All Slide Categories' ),
			'parent_item'       => __( 'Parent Slide Category' ),
			'parent_item_colon' => __( 'Parent Slide Category:' ),
			'edit_item'         => __( 'Edit Slide Category' ),
			'view_item'         => __( 'View Slide Category' ),
			'update_item'       => __( 'Update Slide Category' ),
			'add_new_item'      => __( 'Add New Slide Category' ),
			'new_item_name'     => __( 'New Slide Category Name' ),
			'menu_name'         => __( 'Slide Categories' ),
			'not_found'         => __( 'No slide categories found'),
			'no_terms'          => __( 'No categories found' ) 
		);
	
		$args = array(
			'labels'            => $labels,
			'description'       => __( 'Custom taxonomy for GlitterCape plugin', 'textdomain'),
			'public'            => true,
			'publicly_queryable'=> true,
			'hierarchical'      => true, 
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => false,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'slide_categories', 'hierarchical' => true ),
			'args'              => array( 'usage' => 'For GlitterCape Plugin') 
		);
	
		register_taxonomy( 'cape_slide_categories', array( 'cape_slide' ), $args );

	}

	public function register_cape_post_type(){

		$labels = array(
			'name'                     => _x( 'Cape Slides', 'Slides used in the GlitterCape slider', 'textdomain' ),
			'singular_name'            => _x( 'Cape Slide', 'A single Cape Slide', 'textdomain' ),
			'menu_name'                => _x( 'Cape Slides', 'Admin Menu text', 'textdomain' ),
			'add_new'                  => __( 'Add New', 'textdomain' ),
			'add_new_item'             => __( 'Add New Slide', 'textdomain' ),
			'new_item'                 => __( 'New Slide', 'textdomain' ),
			'edit_item'                => __( 'Edit Slide', 'textdomain' ),
			'view_item'                => __( 'View Slide', 'textdomain' ),
			'view_items'               => __( 'View Slides', 'textdomain' ),
			'all_items'                => __( 'All Slides', 'textdomain' ),
			'search_items'             => __( 'Search Slides', 'textdomain' ),
			'parent_item_colon'        => __( 'Parent Slides:', 'textdomain' ),
			'archives'                 => __( 'Cape Archives', 'textdomain' ),
			'attributes'               => __( 'Slide Attributes', 'texdomain'), 
			'not_found'                => __( 'No Slides found.', 'textdomain' ),
			'not_found_in_trash'       => __( 'No Slides found in Trash.', 'textdomain' ),
			'featured_image'           => _x( 'Slide Cover Image', 'Overrides the “Featured Image” phrase for this post type', 'textdomain' ),
			'set_featured_image'       => _x( 'Set slide cover image', 'Overrides the “Set featured image” phrase for this post type', 'textdomain' ),
			'remove_featured_image'    => _x( 'Remove slide cover image', 'Overrides the “Remove featured image” phrase for this post type', 'textdomain' ),
			'use_featured_image'       => _x( 'Use as slide cover image', 'Overrides the “Use as featured image” phrase for this post type', 'textdomain' ),
			'archives'                 => _x( 'Cape Slide archives', 'The post type archive label used in nav menus. Default “Post Archives”.', 'textdomain' ),
			'insert_into_item'         => _x( 'Insert into Slide', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post).', 'textdomain' ),
			'uploaded_to_this_item'    => _x( 'Uploaded to this Slide', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post).', 'textdomain' ),
			'filter_items_list'        => _x( 'Filter Slides list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”.', 'textdomain' ),
			'items_list_navigation'    => _x( 'Slides list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”.', 'textdomain' ),
			'items_list'               => _x( 'Slides list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”.', 'textdomain' ),
			'item_published'		   => _x( 'Slide Published', 'Label for when a Slide is published', 'textdomain'),
			'item_published_privately' => _x( 'Slide Published privately', 'Label for when a Slide is published', 'textdomain'),
			'item_reverted_to_draft'   => __( 'Slide reverted to draft', 'textdomain'),
			'item_scheduled'           => __( 'Slide scheduled', 'textdomain'),
			'item_updated'             => __( 'Slide updated', 'textdomain'),
			'item_link'                => __( 'Slide Link', 'textdomain'),
			'item_link_description'    => __( 'Link to a Slide', 'textdomain')
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Slides for usage in the Glitter Cape Slider', 'textdomain' ),
			'public'             => true,
			'hierarchical'       => false,
			'exclude_from_search'=> false,
			'show_in_menu'       => $this->parent_menu_slug,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,
			'show_in_rest'       => true, 
			'publicly_queryable' => true,
			'show_ui'            => true,
			'query_var'          => true,
			'map_meta_cap'       => true, 
			'rewrite'            => array( 'slug' => 'cape_slides', 'with_front' => true ),
			'taxonomies'         => array( 'cape_slide_categories', 'post_tag', 'terms'), 
			'has_archive'        => true,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'trackbacks' ),
			'can_export'         => true,
			'delete_with_user'   => true
			
		);

		register_post_type( 'cape_slide', $args );

	}
}
