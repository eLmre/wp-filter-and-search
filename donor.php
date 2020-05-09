<?php
/*
Plugin Name: Donor
Plugin URI: https://github.com/eLmre/wp-filter-and-search
Description:
Author:
Version: 0.0.3
*/

// Register Custom Shortcode
add_shortcode( 'donor', 'donor_callback' );
function donor_callback( $atts ) {
    $atts = shortcode_atts( array(), $atts );
    ob_start();
    require( 'markup.php');
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

add_action( 'wp_enqueue_scripts', 'donor_assets');
function donor_assets() {
    global $post;

    if( has_shortcode( $post->post_content, 'donor') ) {
        /* If post content has [donor] init CSS and JS */

        $rand_ver = rand();
        $settings = (array) get_option( 'donor-settings' );

        /* CSS */
        wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );
        wp_register_style('donor-lato', '//fonts.googleapis.com/css?family=Lato&display=swap', array(), $rand_ver, 'all');
        wp_enqueue_style('donor-lato');
        wp_register_style('donor-bootstrap', plugins_url('/assets/libs/bootstrap/css/bootstrap.min.css', __FILE__), array(), '4.3.1', 'all');
        wp_enqueue_style('donor-bootstrap');
        wp_register_style('donor-fontawesome', plugins_url('/assets/libs/fontawesome/css/all.min.css', __FILE__), array(), '5.8.2', 'all');
        wp_enqueue_style('donor-fontawesome');
        wp_register_style('donor-style', plugins_url('/assets/css/donor.css', __FILE__), array(), rand(), 'all');
        wp_enqueue_style('donor-style');

        /* JS */
        if( $settings['google_map_api'] ) {
            wp_enqueue_script( 'google-maps-api', '//maps.googleapis.com/maps/api/js?key='.$settings['google_map_api'], array(), $rand_ver, true );
        }
        wp_register_script('donor-bootstrap', plugins_url('/assets/libs/bootstrap/js/bootstrap.min.js', __FILE__), array(), $rand_ver, true);
        wp_enqueue_script('donor-bootstrap');
        wp_register_script('donor-fontawesome', plugins_url('/assets/libs/fontawesome/js/all.min.js', __FILE__), array(), $rand_ver, true);
        wp_enqueue_script('donor-fontawesome');
        wp_register_script('donor-autocomplete', plugins_url('/assets/libs/autocomplete/jquery.auto-complete.min.js', __FILE__), $rand_ver, '1.0.7', true);
        wp_enqueue_script('donor-autocomplete');
        wp_register_script('donor-script', plugins_url('/assets/js/donor.js', __FILE__), array(), $rand_ver, true);
        wp_enqueue_script('donor-script');

        $params = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('donor_nonce_value'),
            'google_map_status' => ( $settings['google_map_status'] ) ? $settings['google_map_status'] : '0'
        );
        wp_localize_script( 'donor-script', 'global', $params );
    }
}

// Register Custom Post Type
function organization_post_type() {

    $labels = array(
        'name'                  => 'Organizations',
        'singular_name'         => 'Organization',
        'menu_name'             => 'Organizations',
        'name_admin_bar'        => 'Organizations',
        'archives'              => 'Item Archives',
        'attributes'            => 'Item Attributes',
        'parent_item_colon'     => 'Parent Item:',
        'all_items'             => 'All Items',
        'add_new_item'          => 'Add New Organization',
        'add_new'               => 'Add New',
        'new_item'              => 'New Item',
        'edit_item'             => 'Edit Item',
        'update_item'           => 'Update Item',
        'view_item'             => 'View Item',
        'view_items'            => 'View Items',
        'search_items'          => 'Search Item',
        'not_found'             => 'Not found',
        'not_found_in_trash'    => 'Not found in Trash',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into item',
        'uploaded_to_this_item' => 'Uploaded to this item',
        'items_list'            => 'Items list',
        'items_list_navigation' => 'Items list navigation',
        'filter_items_list'     => 'Filter items list',
    );
    $args = array(
        'label'                 => 'organization',
        'labels'                => $labels,
        'supports'              => array( 'title', 'custom-fields' ),
        'taxonomies'            => array( 'org_cat', 'org_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-screenoptions',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => false,
        'capability_type'       => 'post',
        'show_in_rest'          => false,
        'register_meta_box_cb'  => 'add_organization_metaboxes',
    );
    register_post_type( 'organization', $args );

}
add_action( 'init', 'organization_post_type', 0 );

// Register Custom Taxonomy
function organization_locations_taxonomy() {
    $labels = array(
        'name'                       => 'Locations',
        'singular_name'              => 'Location',
        'menu_name'                  => 'Locations',
        'all_items'                  => 'All Locations',
        'parent_item'                => 'Parent Item',
        'parent_item_colon'          => 'Parent Item:',
        'new_item_name'              => 'New Location Name',
        'add_new_item'               => 'Add New Location',
        'edit_item'                  => 'Edit Location',
        'update_item'                => 'Update Item',
        'view_item'                  => 'View Item',
        'separate_items_with_commas' => 'Separate items with commas',
        'add_or_remove_items'        => 'Add or remove items',
        'choose_from_most_used'      => 'Choose from the most used',
        'popular_items'              => 'Popular Items',
        'search_items'               => 'Search Items',
        'not_found'                  => 'Not Found',
        'no_terms'                   => 'No items',
        'items_list'                 => 'Items list',
        'items_list_navigation'      => 'Items list navigation',
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => false,
        'show_in_rest'               => false,
    );
    register_taxonomy( 'org_location', array( 'organization' ), $args );
}
add_action( 'init', 'organization_locations_taxonomy', 0 );

// Register Custom Taxonomy
function organization_category_taxonomy() {
    $labels = array(
        'name'                       => 'Category',
        'singular_name'              => 'Category',
        'menu_name'                  => 'Categories',
        'all_items'                  => 'All Categories',
        'parent_item'                => 'Parent Item',
        'parent_item_colon'          => 'Parent Item:',
        'new_item_name'              => 'New Category Name',
        'add_new_item'               => 'Add New Category',
        'edit_item'                  => 'Edit Category',
        'update_item'                => 'Update Item',
        'view_item'                  => 'View Item',
        'separate_items_with_commas' => 'Separate items with commas',
        'add_or_remove_items'        => 'Add or remove items',
        'choose_from_most_used'      => 'Choose from the most used',
        'popular_items'              => 'Popular Items',
        'search_items'               => 'Search Items',
        'not_found'                  => 'Not Found',
        'no_terms'                   => 'No items',
        'items_list'                 => 'Items list',
        'items_list_navigation'      => 'Items list navigation',
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => false,
        'show_in_rest'               => false,
    );
    register_taxonomy( 'org_category', array( 'organization' ), $args );

}
add_action( 'init', 'organization_category_taxonomy', 0 );

// Register meta field for org_category
require_once ('org_category_meta.php');
// Register meta field for org_location
require_once ('org_location_meta.php');
// Register meta field for organization post type
require_once ('organization_meta.php');
// Options page
require_once ('option-page.php');

function request_get( $key, $default = '' ) {

    // If not request set
    if ( ! isset( $_REQUEST[ $key ] ) || empty( $_REQUEST[ $key ] ) ) {
        return $default;
    }

    // Set so process it
    return strip_tags( (string) wp_unslash( $_REQUEST[ $key ] ) );
}

add_action('wp_ajax_donor_action', 'donor_action_callback');
add_action('wp_ajax_nopriv_donor_action', 'donor_action_callback');
function donor_action_callback( $request = array(), $return_qwerty = false, $autocomplete = false ) {

    $nonce = ( !empty($request['nonce']) ) ? $request['nonce'] : $_POST['nonce'];

    if( !wp_verify_nonce( $nonce, 'donor_nonce_value') ) {
        return false;
    }

    $return = [];
    $search = ( !empty( $request['search'] ) ) ? $request['search'] : $_POST['search'];
    $page = ( !empty($request['page']) ) ? $request['page'] : $_POST['page'];
    $page = ( !empty($page) ) ? $page : 1;
    $locations = ( isset($request['locations']) ) ? $request['locations'] : $_POST['locations'];
    $categories = ( isset($request['categories']) ) ? $request['categories'] : $_POST['categories'];
    $args = [
        'post_type' => 'organization',
        'posts_per_page' => '6',
        'paged' => $page,
        'orderby' => 'rand',
        'order' => 'ASC'
    ];

    if( $autocomplete == false ) {

        if (!empty($search)) {
            $get_terms_args = array(
                'taxonomy' => array('org_location', 'org_category'),
                'hide_empty' => true,
                'fields' => 'id=>slug',
                'name__like' => $search
            );
            $get_terms = get_terms($get_terms_args);
            $terms = array_values($get_terms);
            $args['tax_query']['relation'] = 'AND';
        }

        if (!empty($search) && empty($terms)) {

            $meta_query[] = array(
                'key' => 'fundraiseup_url',
                'value' => $search,
                'compare' => 'LIKE'
            );

            $meta_query[] = array(
                'key' => 'org_keyword',
                'value' => $search,
                'compare' => 'LIKE'
            );

            //if there is more than one meta query 'or' them
            if (count($meta_query) > 0) {
                $meta_query['relation'] = 'OR';
            }

            $args['_meta_or_title'] = $search; //not using 's' anymore
            $args['meta_query'] = $meta_query;
        }

        if (!empty($locations) || !empty($terms)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'org_location',
                'field' => 'slug',
                'terms' => (!empty($terms)) ? $terms : explode(',', $locations)
            );
        }

        if (!empty($categories) || !empty($terms)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'org_category',
                'field' => 'slug',
                'terms' => (!empty($terms)) ? $terms : explode(',', $categories)
            );
        }

    } else {

        if ( !empty($search) ) {
            $args['s'] = $search;
        }

    }

    $qwerty = new WP_Query( $args );

    if( isset($return_qwerty) && $return_qwerty == true ) {
        return $qwerty;
    }

    if ($qwerty->have_posts()) {
        ob_start();
        while($qwerty->have_posts()) : $qwerty->the_post();
        $post_id = get_the_ID();
        $fundraiseup_url = get_post_meta( $post_id, 'fundraiseup_url', true );
        if( !empty($fundraiseup_url) ) { ?>
        <div class="col-12 col-md-6 col-lg-4 pb-5" data-id="<?php echo $post_id; ?>"><div class="org-list__item"><?php echo $fundraiseup_url; ?></div></div><?php } ?>
        <?php
        endwhile;
        $return['html'] = ob_get_clean();
    } else {
        $return['html'] = 0;
    }

    $return['max_num_pages'] = ( !empty( $qwerty->max_num_pages ) ) ? $qwerty->max_num_pages : 0;

    wp_send_json_success( $return );
}

/**
 * Using meta query ('meta_query') with a search query ('s')
 * https://wordpress.stackexchange.com/questions/78649/using-meta-query-meta-query-with-a-search-query-s
 */
add_action( 'pre_get_posts', function( $q ) {
    if( $title = $q->get( '_meta_or_title' ) )
    {
        add_filter( 'get_meta_sql', function( $sql ) use ( $title )
        {
            global $wpdb;

            // Only run once:
            static $nr = 0;
            if( 0 != $nr++ ) return $sql;

            // Modified WHERE
            $sql['where'] = sprintf(
                " AND ( %s OR %s ) ",
                $wpdb->prepare( "{$wpdb->posts}.post_title like '%%%s%%'", $title),
                mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
            );

            return $sql;
        });
    }
});

/*
 *  Since the changed behaviour of esc_sql() in WordPress 4.8.3
 *  it is easy to use the % character as a placeholder for the following search and replace.
 */
function my_posts_where( $where ) {
    global $wpdb;
    $where = str_replace(
        "wp_posts.post_title = '%",
        "wp_posts.post_title LIKE '%",
        $wpdb->remove_placeholder_escape($where)
    );
    return $where;
}
add_filter('posts_where', 'my_posts_where');
add_filter('posts_orderby', 'my_posts_where');


add_action( 'admin_menu' , 'remove_post_custom_fields' );
function remove_post_custom_fields() {
    remove_meta_box( 'postcustom' , 'organization' , 'normal' );
}


add_action( 'wp_ajax_donor_search', 'autocomplete_donor_search' );
add_action( 'wp_ajax_nopriv_donor_search', 'autocomplete_donor_search' );
function autocomplete_donor_search() {
    $request = [];

    if( !empty($_POST['nonce']) ) {
        $request['nonce'] = $_POST['nonce'];
    }

    if( !empty($_POST['search']) ) {
        $request['search'] = $_POST['search'];
    }

    $results = donor_action_callback($request, true, true);

    $items = [];

    if ( !empty( $results->posts ) ) {

        foreach ( $results->posts as $result ) {
            $items[] = $result->post_title;
        }

    }

    wp_send_json_success( $items );
}


//add_action('init', 'example_hide');
function example_hide() {
    if ( !empty($_GET['update_all_posts']) && $_GET['update_all_posts'] = '1' ) {
        $my_posts = get_posts(array('post_type' => 'organization', 'numberposts' => -1));
        foreach ($my_posts as $my_post) {
            $old_org_keyword = get_post_meta( $my_post->ID, 'org_keyword', true);
            update_org_meta($my_post->ID, $old_org_keyword);
        }
    }
}

add_filter( 'pre_post_update', 'save_org_meta' );
function save_org_meta( $post_id ) {
    $slug = 'organization';

    if ( $slug != $_POST['post_type'] )
        return;

    update_org_meta($post_id, $_POST['org_keyword']);
}

function update_org_meta( $post_id, $old_org_keyword ) {
    $post = get_post( $post_id );
    $meta_values = get_post_meta( $post_id, 'fundraiseup_url', true);
    $str = $post->post_title .' '. $meta_values;
    preg_match_all('/\w+\'\w+/', $str, $matches, PREG_SET_ORDER, 0);
    $keywords = [];
    foreach ( $matches as $key ) {
        $keywords[] = preg_replace("/'/", '', $key[0] );
    }
    $old_org_keyword = preg_replace('/\s+/','',$old_org_keyword);
    $old_org_keyword = explode(',', $old_org_keyword );
    $meta_string = implode(", ", array_unique( array_merge($keywords,$old_org_keyword) ) );
    update_post_meta($post_id, 'org_keyword', $meta_string);
}


/**
 * just add a body class
 */
function donor_page_body_class( $classes ) {
    global $post;
    if( has_shortcode( $post->post_content, 'donor') ) {
        $classes[] = 'donor-page';
        return $classes;
    }
}
add_filter( 'body_class', 'donor_page_body_class' );


/**
 * Activate the plugin.
 */
function donor_activate() {
    // Check if ACF Pro activated
    if( !is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
        // Show notification if not
        set_transient( 'donor_admin_notice', array(
            'message' => 'Plugin <b>Donor</b> requires <b>"Advanced Custom Fields PRO"</b> to provide full capabilities.',
            'class' => 'notice-info'), // css class for notice bar
            5 // time
        );
    }
}
register_activation_hook( __FILE__, 'donor_activate' );


/**
 * This function helps to show notifications on admin-panel.
 */
add_action( 'admin_notices', 'donor_admin_notification' );
function donor_admin_notification() {
    $donor_admin_notice = get_transient( 'donor_admin_notice' );
    if( $donor_admin_notice ) { ?>
        <div class="notice <?php echo ($donor_admin_notice['class']) ? $donor_admin_notice['class'] : 'notice-warning'; ?> is-dismissible">
            <p><?php echo $donor_admin_notice['message']; ?></p>
        </div>
        <?php
        delete_transient( 'mp-admin-notice-activation' );
    }
}


// Just Setting link in plugin list
function donor_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=donor-plugin">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'donor_settings_link' );


/**
 * The Google Maps field requires the following APIs;
 * Maps JavaScript API, Geocoding API and Places API.
 */
function donor_acf_init() {
    $settings = (array) get_option( 'donor-settings' );
    // Test key = AIzaSyC83pQksz22QdlBjEW7xBcMM-fVCvDeLQI
    if( !empty($settings['google_map_api']) ) {
        acf_update_setting('google_api_key', $settings['google_map_api']);
    }
    if( !empty($settings['google_map_status']) && $settings['google_map_status'] == 1 ) {
        require_once ('acf-schema.php');
    }
}
add_action('acf/init', 'donor_acf_init');
