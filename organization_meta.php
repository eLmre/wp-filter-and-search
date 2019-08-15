<?php
/**
 * Adds a metabox to the right side of the screen under the “Publish” box
 */
function add_organization_metaboxes() {
    add_meta_box(
        'org_fundraiseup_url',
        'Fundraiseup',
        'org_fundraiseup_url_callback',
        'organization',
        'normal',
        'high'
    );

    add_meta_box(
        'org_keyword',
        'Keywords',
        'org_keyword_callback',
        'organization',
        'normal',
        'high'
    );

}

/**
 * Output the HTML for the metabox.
 */
function org_fundraiseup_url_callback() {
    global $post;

    // Nonce field to validate form request came from current site
    wp_nonce_field( basename( __FILE__ ), 'organization_fields' );

    // Get the location data if it's already been entered
    $location = get_post_meta( $post->ID, 'fundraiseup_url', true );

    // Output the field
    echo '<input type="text" name="fundraiseup_url" value="' . esc_attr( $location )  . '" class="widefat">';
}

/**
 * Output the HTML for the metabox.
 */
function org_keyword_callback() {
    global $post;

    // Nonce field to validate form request came from current site
    //wp_nonce_field( basename( __FILE__ ), 'organization_fields' );

    // Get the location data if it's already been entered
    $keyword = get_post_meta( $post->ID, 'org_keyword', true );

    // Output the field
    echo '<input type="text" name="org_keyword" value="' . esc_attr( $keyword )  . '" class="widefat">';
}

/**
 * Save the metabox data
 */
function org_save_organization_meta( $post_id, $post ) {
    // Return if the user doesn't have edit permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }
    // Verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times.
    if ( ! isset( $_POST['organization_fields'] ) || ! wp_verify_nonce( $_POST['organization_fields'], basename(__FILE__) ) ) {
        return $post_id;
    }

    // Now that we're authenticated, time to save the data.
    // This sanitizes the data from the field and saves it into an array $events_meta.
    $events_meta['fundraiseup_url'] = $_POST['fundraiseup_url'];

//    if ( isset( $_POST['org_keyword'] ) ) {
//        $events_meta['org_keyword'] = $_POST['org_keyword'];
//    }


    // Cycle through the $events_meta array.
    // Note, in this example we just have one item, but this is helpful if you have multiple.
    foreach ( $events_meta as $key => $value ) :
        // Don't store custom data twice
        if ( 'revision' === $post->post_type ) {
            return;
        }
        if ( get_post_meta( $post_id, $key, false ) ) {
            // If the custom field already has a value, update it.
            update_post_meta( $post_id, $key, $value );
        } else {
            // If the custom field doesn't have a value, add it.
            add_post_meta( $post_id, $key, $value);
        }
        if ( ! $value ) {
            // Delete the meta key if there's no value
            delete_post_meta( $post_id, $key );
        }
    endforeach;
}
add_action( 'save_post', 'org_save_organization_meta', 1, 2 );