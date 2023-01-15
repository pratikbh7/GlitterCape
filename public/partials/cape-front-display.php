<?php
if ( ! function_exists( 'get_attachment_id' ) ) {
    /**
     * Get the Attachment ID for a given image URL.
     *
     * @link   http://wordpress.stackexchange.com/a/7094
     *
     * @param  string $url
     *
     * @return boolean|integer
     */
    function get_attachment_id( $url ) {

        $dir = wp_upload_dir();

        // baseurl never has a trailing slash
        if ( false === strpos( $url, $dir['baseurl'] . '/' ) ) {
            // URL points to a place outside of upload directory
            return false;
        }

        $file  = basename( $url );
        $query = array(
            'post_type'  => 'attachment',
            'fields'     => 'ids',
            'meta_query' => array(
                array(
                    'key'     => '_wp_attached_file',
                    'value'   => $file,
                    'compare' => 'LIKE',
                ),
            )
        );

        // query attachments
        $ids = get_posts( $query );

        if ( ! empty( $ids ) ) {

            foreach ( $ids as $id ) {

                // first entry of returned array is the URL
                if ( $url === array_shift( wp_get_attachment_image_src( $id, 'full' ) ) )
                    return $id;
            }
        }

        $query['meta_query'][0]['key'] = '_wp_attachment_metadata';

        // query attachments again
        $ids = get_posts( $query );

        if ( empty( $ids) )
            return false;

        foreach ( $ids as $id ) {

            $meta = wp_get_attachment_metadata( $id );

            foreach ( $meta['sizes'] as $size => $values ) {

                if ( $values['file'] === $file && $url === array_shift( wp_get_attachment_image_src( $id, $size ) ) )
                    return $id;
            }
        }

        return false;
    }
}
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
    <div class = "next-right-arrow">
    </div>
    <div class = "next-left-arrow">
    </div>
    <?php
    foreach( $cape_slides as $slide ){
        $slide_title = $slide->post_title;
        if( in_array( $slide_title, $slides) ){
            //instead of using wp_query to retrieve attachment id and get image path:   
            $image_url = get_the_post_thumbnail_url( $slide->ID, 'full');
            $image_id = get_attachment_id( $image_url );
            $image_path = wp_get_original_image_path( $image_id );
            $image_dimensions = getimagesize( $image_path );
            $image_width = $image_dimensions[0];
            $image_height = $image_dimensions[1];
            $aspect_ratio = ( $image_width / $image_height ); 
            ?>
            <div id = <?php _e( $slide->post_name . '_slide_container')?> class="cape_slides" data-aspect-ratio="<?php echo $aspect_ratio; ?>" style="background-image:url('<?php echo $image_url; ?>');">
                <p><?php _e( $slide_title ) ?> </p>
            </div> 
        <?php    
        }
    }
    ?>
</div>
<!-- slide positioning -->
<script>
    cape_interactive();
    function cape_interactive(event){
        const container = document.getElementById('cape_slide_container');
        function container_offsets(){
            let offsets = container.getBoundingClientRect();
            let top = offsets.top;
            let left = offsets.left;
            return {
                left: left + window.scrollX,
                top: top + window.scrollY
            };
        }
        const container_position = container_offsets();
        const container_y = container_position.top;
        const container_x = container_position.left;
        function get_computed_styles(elem){
            return window.getComputedStyle(elem);
        }
        const container_styles = get_computed_styles(container);
        const container_width = parseInt(container_styles.getPropertyValue('width'));
        const slides = document.getElementsByClassName('cape_slides');
        const right_arrow = container.querySelector('.next-right-arrow');
        const left_arrow = container.querySelector('.next-left-arrow'); 
        var image_index = 0;
        const slide_length = slides.length;
        var new_slide_width, new_slide_height, horizontal_offset, vertical_offset, scale_max_dim, exposed_pixels, additive_placement = 0, set_zIndex, set_opcaity, negative_offset, scale_slide;
        set_zIndex = slide_length;
        set_opacity = 1;
        const max_dim =  parseInt(container_styles.getPropertyValue('height'));
        right_arrow.style.left = ( container_width / 2 ) + ( max_dim / 2) + 48 + 'px'; 
        right_arrow.style.top = container_y + ( ( max_dim / 2 ) - 25 ) + 'px'; 
        left_arrow.style.right= ( container_width / 2 ) + ( max_dim / 2) + 48 + 'px';
        left_arrow.style.top = container_y + ( ( max_dim / 2 ) - 25 ) + 'px';
        const x_offsets = [ 0, 0, 0, 0, 0, 0, 0, 0, 0];
        // iteration method to be refined https://stackoverflow.com/questions/16053357/what-does-foreach-call-do-in-javascript
        Array.prototype.forEach.call(slides, function(element) {
                element.style.opacity = set_opacity; 
                element.style.zIndex = set_zIndex;
                scale_slide = 1 -( 0.15 * image_index )
                scale_max_dim = ( max_dim ) * scale_slide;
                exposed_pixels = ( scale_max_dim / Math.pow( 2, (image_index + 1) ) ) ;
                //placement must be additive in order to consistently place slides wrt the scaled dimensions
                additive_placement = ( image_index === 0 ) ? 0 : additive_placement + exposed_pixels;
                new_slide_height = new_slide_width = scale_max_dim;
                element.style.height = new_slide_height + 'px';
                element.style.width = new_slide_width + 'px';
                vertical_offset = container_y + (max_dim - new_slide_height) / 2;
                horizontal_offset = container_x + (container_width - new_slide_width) / 2 + (max_dim - new_slide_width) / 2 + additive_placement;
                element.style.left =  horizontal_offset + 'px';
                element.style.top =  vertical_offset + 'px';
                element.cape_state = {
                    opacity : set_opacity,
                    scale : scale_slide,
                    slide_width : new_slide_width,
                    slide_height : new_slide_height,
                    horizontal_offset : horizontal_offset,
                    zindex : set_zIndex
                }
                x_offsets[ 4 + image_index ] = horizontal_offset;
                if( image_index >= 1){
                    negative_offset = container_x + (container_width - new_slide_width) / 2 - (max_dim - new_slide_width) / 2 - additive_placement;
                    x_offsets [ 4 - image_index ] = negative_offset;
                }
                ++image_index;
                --set_zIndex;
                set_opacity = set_opacity - 0.2;
                set_opacity = parseFloat(set_opacity.toFixed(2)); //map opacity to a fixed floating point
            });
            container.properties = { maximum_dimension : max_dim,
                x_offsets : x_offsets,
                x_position: container_x,
                y_position : container_y};
}
</script>

