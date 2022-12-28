<?php 
$cape_nonce = wp_create_nonce( 'cape_admin_nonce' );

$templates = array( 'Template1', 'Template2', 'Template3', 'Template4');
$templates_dropdown =  '<select required id="cape_templates" name="cape_admin[template_select]">
    <option value="">'.__( 'Pick a template', 'textdomain' ).'</option>';

$slide_category_dropdown = '<select required id="user_slide_categories" name="cape_admin[category_select]">
    <option value="">'.__( 'Pick a slide category', 'textdomain' ).'</option>';

foreach( $templates as $value){
    $template_value = esc_html( $value );
    $templates_dropdown .= '<option value="' . $template_value . '">' . $template_value . '</option>' . "\n";
}

$cat_args = array( 
    'taxonomy'   => 'cape_slide_categories',
    'hide_empty' => true,
    'fields'     => 'all');
$get_slide_categories = get_terms( $cat_args );
foreach( $get_slide_categories as $key=>$value ){
    $cat_value = esc_html( $value->name );
    $slide_category_dropdown .= '<option value="' . $cat_value . '">' . $cat_value . '</option>' . "\n";
}

$close_select_tags = array( &$templates_dropdown, &$slide_category_dropdown );
foreach( $close_select_tags as &$value){
    $value .= '</select>';
} 
    
?>
<div  class="cape_admin_interface">
    <div class="cape_nonce_field">
        <input type="hidden" name="action" value="cape_form_response">
        <input type="hidden" name="cape_nonce" value="<?php echo $cape_nonce ?>" />
    </div>
    <div class="cape_input_group">
        <label for="cape_templates"><?php _e( "Templates", 'textdomain' ); ?></label>
        <?php echo $templates_dropdown; ?>
    </div>
    <div class="cape_input_group">
        <label for="cape_slider_name"><?php _e( "Name your slider:", 'textdomain' ); ?></label>
        <input required type="text" name="cape_admin[slider_name]" id="cape_slider_name" value=""/>
    </div>
    <div class="cape_input_group">
        <label for="user_slide_categories"><?php _e( "Slide Category:", 'textdomain' ); ?></label>
        <?php echo $slide_category_dropdown; ?>
    </div>
    <div class="cape_input_group">
        <fieldset id="user_slides">
            <!-- legend tag doesn't respect inline-block css -->
            <p id="caption_replacer"><?php _e("Include Slides:", 'textdomain'); ?></p>
            <p id="cat_selection_notice"><?php _e("Select a slide category first", 'textdomain') ?></p>
        </fieldset>
    </div>
</div>
