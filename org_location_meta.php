<?php

// Add term page
add_action( 'org_location_add_form_fields', 'org_location_taxonomy_add_new_meta_field', 10, 2 );
function org_location_taxonomy_add_new_meta_field() {
    // this will add the custom meta field to the add new term page
    ?>

    <div class="form-field">
        <label for="term_meta[exclude_filter]">Exclude from filter</label>
        <input type="checkbox" name="term_meta[exclude_filter]" id="term_meta[exclude_filter]" value="1">
        <p class="description"></p>
    </div>
    <?php
}

// Edit term page
add_action( 'org_location_edit_form_fields', 'org_location_taxonomy_edit_meta_field', 10, 2 );
function org_location_taxonomy_edit_meta_field($term) {
    // put the term ID into a variable
    $t_id = $term->term_id;
    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option( "org_location_$t_id" ); ?>

    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="term_meta[exclude_filter]">Exclude from filter:</label>
        </th>
        <td>
            <input type="checkbox" name="term_meta[exclude_filter]" id="term_meta[exclude_filter]" value="1" <?php if ( isset ( $term_meta['exclude_filter'] ) ) checked( $term_meta['exclude_filter'][0], '1' ); ?>>
            <p class="description"></p>
        </td>
    </tr>
    <?php
}

// Save extra taxonomy fields callback function.
add_action( 'edited_org_location', 'save_org_location_custom_meta', 10, 2 );
add_action( 'create_org_location', 'save_org_location_custom_meta', 10, 2 );
function save_org_location_custom_meta( $term_id ) {
    $t_id = $term_id;
    $term_meta = get_option( "org_location_$t_id" );

    if ( isset( $_POST['term_meta'] ) ) {
        $cat_keys = array_keys( $_POST['term_meta'] );

        foreach ( $cat_keys as $key ) {
            if ( isset ( $_POST['term_meta'][$key] ) ) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }

        // Checks for input and saves - save checked as 1 and unchecked at 0
        if( isset( $_POST['term_meta']['exclude_filter'] ) ) {
            $term_meta['exclude_filter'] = '1';
        } else {
            $term_meta['exclude_filter'] = '0';
        }

        // Save the option array.
        update_option( "org_location_$t_id", $term_meta );
    } else {
        $term_meta['exclude_filter'] = '0';
        update_option( "org_location_$t_id", $term_meta );
    }


}
