<?php
$slide_category = $slide_data[0];
$slides = $slide_data[1];
$slides = explode( ",", $slides );
$args = array(  'numberposts'  => -1,
                'post_type'    => 'cape_slide',
                'post_status'  => 'publish',
                'perm'         => 'readable' );
$cape_slides = get_posts( $args );
$index = 0; ?>
<div id = "cape_slide_container">
<?php
foreach( $cape_slides as $slide ){
    $slide_title = $slide->post_title;
    if( in_array( $slide_title, $slides) ){
        //instead of using wp_query to retrieve attachment id and get image path:   
        $image_url = get_the_post_thumbnail_url( $slide->ID, 'post-thumbnail');
        $image_path = parse_url( $image_url, PHP_URL_PATH );
        $slash_position = ( strpos( $image_path, '/' ) === false) ? strpos( $image_path, '\\' ) : strpos( $image_path, '/');
        if( $slash_position === 0 ){
            //remove leading slash
            $image_path = substr( $image_path, 1 );
        }
        $image_dimensions = getimagesize( $image_path );
        $image_width = $image_dimensions[0];
        $image_height = $image_dimensions[1];
        ?>
        <div id = <?php _e( $slide->post_name . '_slide_container')?> class="cape_slides" data-width="<?php echo $image_width; ?>" data-height="<?php echo $image_height ?>" style="background-image:url('<?php echo $image_url; ?>');">
            <p><?php _e( $slide_title ) ?> </p>
        </div> 
    <?php    
    }
}
?>
</div>

