<?php
defined('ABSPATH') || exit;

function ossigeno_settings_init()
{
    // Register a new setting for "ossigeno" page
    register_setting('ossigeno', 'ossigeno_whatsapp_number');
    register_setting('ossigeno', 'ossigeno_whatsapp_cta');
    register_setting('ossigeno', 'ossigeno_show_scroll_to_top_button');
    register_setting('ossigeno', 'ossigeno_placeholder_image');

    // Add a new section to the "ossigeno" page
    add_settings_section(
        'ossigeno_settings_section',
        __('Theme options', 'ossigeno'),
        'ossigeno_settings_section_callback',
        'ossigeno_options_page'
    );

    // Add a whatsapp number field
    add_settings_field(
        'ossigeno_whatsapp_number_field',
        __('WhatsApp phone number', 'ossigeno'),
        'ossigeno_whatsapp_number_field_callback',
        'ossigeno_options_page',
        'ossigeno_settings_section'
    );

    // Add a whatsapp CTA field
    add_settings_field(
        'ossigeno_whatsapp_cta_field',
        __('WhatsApp label', 'ossigeno'),
        'ossigeno_whatsapp_cta_field_callback',
        'ossigeno_options_page',
        'ossigeno_settings_section'
    );

    // Add an option to show the scroll to top button
    add_settings_field(
        'ossigeno_show_scroll_to_top_button',
        __('Show Scroll to top button', 'ossigeno'),
        'ossigeno_show_scroll_to_top_button_callback',
        'ossigeno_options_page',
        'ossigeno_settings_section'
    );

    // Add an image selector for the placeholder image
    add_settings_field(
        'ossigeno_placeholder_image',
        __('Placeholder image', 'ossigeno'),
        'ossigeno_placeholder_image_callback',
        'ossigeno_options_page',
        'ossigeno_settings_section'
    );
}

function ossigeno_settings_section_callback()
{
    echo __("Customize your website aspect", 'ossigeno');
}

// Add a whatsapp number field
function ossigeno_whatsapp_number_field_callback()
{
    // Get the value of the setting we've registered with register_setting()
    $setting = get_option('ossigeno_whatsapp_number');
    // Output the field
    echo "<input type='text' name='ossigeno_whatsapp_number' value='" . esc_attr($setting) . "'>";
}

// Add a whatsapp CTA field
function ossigeno_whatsapp_cta_field_callback()
{
    // Get the value of the setting we've registered with register_setting()
    $setting = get_option('ossigeno_whatsapp_cta');
    // Output the field
    echo "<input type='text' name='ossigeno_whatsapp_cta' value='" . esc_attr($setting) . "'>";
}

// Add an option to show the scroll to top button
function ossigeno_show_scroll_to_top_button_callback()
{
    // Get the value of the setting we've registered with register_setting()
    $setting = get_option('ossigeno_show_scroll_to_top_button');
    // Output the field
    echo "<input type='checkbox' name='ossigeno_show_scroll_to_top_button' " . checked($setting, 'on', false) . ">";
}

// Add an image selector for the placeholder image
function ossigeno_placeholder_image_callback()
{
    // Get the value of the setting we've registered with register_setting()
    $setting = get_option('ossigeno_placeholder_image');
    $image_url = wp_get_attachment_url($setting);

    // Output the field
    echo '<input type="hidden" id="ossigeno_image_id" name="ossigeno_placeholder_image" value="' . esc_attr($setting) . '">';
    echo '<input id="upload_button" type="button" class="button" value="Select Image" style="margin-right: 1rem;" />';
    echo '<input id="reset_button" type="button" class="button" value="Reset Image" />';

    // Output the image preview
    echo '<div style="margin-top: 1rem;"><img id="ossigeno_image_preview" src="' . esc_attr($image_url) . '" style="max-width: 300px; max-height: 300px;"></div>';

    // Inline JavaScript to handle the Media Library and the reset button
    echo '
    <script>
    document.getElementById("upload_button").addEventListener("click", function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: "Upload Image",
            multiple: false
        }).open()
        .on("select", function(e){
            var uploaded_image = image.state().get("selection").first();
            var image_id = uploaded_image.toJSON().id;
            var image_url = uploaded_image.toJSON().url;
            document.getElementById("ossigeno_image_id").value = image_id;
            document.getElementById("ossigeno_image_preview").src = image_url;
        });
    });

    document.getElementById("reset_button").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("ossigeno_image_id").value = "";
        document.getElementById("ossigeno_image_preview").src = "";
    });
    </script>';
}

function ossigeno_options_page()
{
    // Add top level menu page
    add_options_page(
        __('Theme options', 'ossigeno'),
        __('Theme options', 'ossigeno'),
        'manage_options',
        'ossigeno_options_page',
        'ossigeno_options_page_html'
    );
}

function ossigeno_options_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // show error/update messages
    settings_errors('ossigeno_messages');
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "ossigeno"
            settings_fields('ossigeno');
            // output setting sections and their fields
            do_settings_sections('ossigeno_options_page');
            // output save settings button
            submit_button('Save Settings');
            ?>
        </form>
    </div>
<?php
}

add_action('admin_menu', 'ossigeno_options_page');
add_action('admin_init', 'ossigeno_settings_init');

function ssnail_ossigeno_customize_register( $wp_customize ) {
    //Logotype - title_tagline
    $wp_customize->add_setting('ssnail_custom_logotype');

    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'ssnail_custom_logotype', array(
            'label' => __( 'Logotype', 'ossigeno' ),
            'section' => 'title_tagline',
            'settings'       => 'ssnail_custom_logotype',
            'priority' => 8,
            'mime_type' => 'image'
    ) ) );
}
add_action( 'customize_register', 'ssnail_ossigeno_customize_register' );