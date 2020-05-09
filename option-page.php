<?php

add_action( 'admin_menu', 'donor_admin_menu' );
function donor_admin_menu() {
    add_options_page(
        'Donor Plugin', // page title
        'Donor Options', // menu title
        'manage_options', // page location
        'donor-plugin', // menu slug
        'donor_options_page' // function
    );
}

add_action( 'admin_init', 'donor_options_admin_init' );
function donor_options_admin_init() {
    register_setting(
        'donor-settings-fields', // option group
        'donor-settings', // name of option to be saved in database
        'donor_options_validate_and_sanitize' // input validation callback
    );

    add_settings_section( 'section-1', 'Google Maps', 'section_1_callback', 'donor-plugin' );

    add_settings_field( 'google_map_status', 'Status', 'google_map_status_callback', 'donor-plugin', 'section-1' );
    add_settings_field( 'google_map_api', 'API Key', 'google_map_api_callback', 'donor-plugin', 'section-1' );
}

function donor_options_page() {
    ?>
    <div class="wrap">
        <h2>Donor Plugin Options</h2>
        <form action="options.php" method="POST">
            <?php settings_fields('donor-settings-fields'); ?>
            <?php do_settings_sections('donor-plugin'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }

function section_1_callback() {
    //echo 'Some help text regarding Section One goes here.';
}

function google_map_status_callback() {
    $settings = (array) get_option( 'donor-settings' );
    $field = "google_map_status";
    $value = esc_attr( (isset($settings[$field])) ? $settings[$field] : '' );
    ?>
    <label for="donor-settings[<?php echo $field; ?>]">
        <input name="donor-settings[<?php echo $field; ?>]" type="checkbox" value="1" <?php echo ($value == '1') ? 'checked' : ''; ?>>
        Display Google Map
    </label>
    <?php
}

function google_map_api_callback() {
    $settings = (array) get_option( 'donor-settings' );
    $field = "google_map_api";
    $value = esc_attr( (isset($settings[$field])) ? $settings[$field] : '' );
    echo "<input type='text' name='donor-settings[$field]' value='$value' />";
}

function donor_options_validate_and_sanitize( $input ) {
    //$settings = (array) get_option( 'donor-settings' );

    if ( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
        $output['google_map_status'] = $input['google_map_status'];
    } else {
        $output['google_map_status'] = 0;
        add_settings_error( 'donor-settings', 'invalid-google_map_status', 'We can\'t enable the Google Maps feature without Advanced Custom Fields PRO plugin' );
    }

    $output['google_map_api'] = $input['google_map_api'];

    return $output;
}