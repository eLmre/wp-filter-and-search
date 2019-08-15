<?php

// Add term page
add_action( 'org_category_add_form_fields', 'org_category_taxonomy_add_new_meta_field', 10, 2 );
function org_category_taxonomy_add_new_meta_field() {
    // this will add the custom meta field to the add new term page
    ?>
    <div class="form-field">
        <label for="term_meta[fontawesome]">FontAwesome CSS class</label>
        <input type="text" name="term_meta[fontawesome]" id="term_meta[fontawesome]" value="">
        <p class="description"><a href="https://fontawesome.com/icons?d=gallery" target="_blank">fontawesome.com/icons</a></p>
    </div>
    <div class="form-field">
        <label for="term_meta[exclude_filter]">Exclude from filter</label>
        <input type="checkbox" name="term_meta[exclude_filter]" id="term_meta[exclude_filter]" value="1">
        <p class="description"></p>
    </div>
    <?php
}

// Edit term page
add_action( 'org_category_edit_form_fields', 'org_category_taxonomy_edit_meta_field', 10, 2 );
function org_category_taxonomy_edit_meta_field($term) {
    // put the term ID into a variable
    $t_id = $term->term_id;
    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option( "taxonomy_$t_id" ); ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="term_meta[fontawesome]">FontAwesome CSS class</label>
        </th>
        <td>
            <input type="text" name="term_meta[fontawesome]" id="term_meta[fontawesome]" value="<?php echo esc_attr( $term_meta['fontawesome'] ) ? esc_attr( $term_meta['fontawesome'] ) : ''; ?>">
            <p class="description"><a href="https://fontawesome.com/icons?d=gallery" target="_blank">fontawesome.com/icons</a></p>
        </td>
    </tr>
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
add_action( 'edited_org_category', 'save_org_category_custom_meta', 10, 2 );
add_action( 'create_org_category', 'save_org_category_custom_meta', 10, 2 );
function save_org_category_custom_meta( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_$t_id" );
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
        update_option( "taxonomy_$t_id", $term_meta );
    }
}
