<div class="container p-0 w-100 JS-Donor">
    <div class="row no-gutters">
        <div class="col-12 col-lg-10 mx-auto">
            <div class="filter">
                <div class="filter__form">
                    <form class="JS-Donor-Form">

                        <input class="JS-Donor-Taxonomy" name="locations" type="hidden" autocomplete="off" value="<?php echo request_get('locations', ''); ?>" aria-label="locations">

                        <input class="JS-Donor-Taxonomy" name="categories" type="hidden" autocomplete="off" value="<?php echo request_get('categories', ''); ?>" aria-label="categories">

                        <div class="filter__row filter__row_search">
                            <input name="search" type="text" class="filter__input JS-Donor-Search" placeholder="Search for an organization…" aria-label="Search organization" autocomplete="off" value="<?php echo request_get('search', ''); ?>">
                            <div class="filter__input-btn">
                                <button class="btn filter__search JS-Donor-SearchBtn" type="button">Search</button>
                            </div>
                        </div>

                        <?php $donor_settings = (array) get_option( 'donor-settings' );
                        if( $donor_settings['google_map_status'] ) { ?>
                        <div class="filter__row JS-Hover">
                            <div class="btn filter__collapse JS-Collapse collapsed">
                                <div class="filter__collapse-text">Map</div>
                                <button class="btn btn-view-all JS-Donor-ViewAll" data-taxonomy="locations" type="button"><i class="ico far fa-redo-alt"></i> View All</button>
                            </div>
                            <div class="collapse filter__collapse-body" id="collapseMap">
                                <div class="locations">
                                    <div class="acf-map JS-Donor-Map"></div>
                                    <div class="hidden JS-Donor-Map-Content">
                                        <?php $org_location = get_terms( 'org_location' );
                                        if( $org_location && ! is_wp_error($org_location) ) { ?>
                                            <?php foreach( $org_location as $term ){ ?>
                                                <?php $google_map = get_field( 'google_map', $term ); ?>
                                                <?php if( $google_map ) { ?>
                                                    <div class="js-marker" data-slug="<?php echo $term->slug; ?>" data-lat="<?php echo $google_map['lat']; ?>" data-lng="<?php echo $google_map['lng']; ?>">
                                                        <div class="js-marker-tooltip">
                                                            <?php echo $term->name; ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="filter__row JS-Hover">
                            <div class="btn filter__collapse JS-Collapse collapsed">
                                <div class="filter__collapse-text">Locations</div>
                                <button class="btn btn-view-all JS-Donor-ViewAll" data-taxonomy="locations" type="button"><i class="ico far fa-redo-alt"></i> View All</button>
                            </div>
                            <div class="collapse filter__collapse-body" id="collapseLocations">
                                <?php
                                $org_location = get_terms( 'org_location' );
                                if( $org_location && ! is_wp_error($org_location) ){
                                    echo '<div class="taxonomy d-flex flex-wrap align-content-start">';
                                    foreach( $org_location as $term ){
                                        $term_meta = get_option( "org_location_$term->term_id" );
                                        if( isset($term_meta['exclude_filter']) && $term_meta['exclude_filter'] == '1' ) {
                                            continue;
                                        }
                                        ?>
                                        <a data-slug="<?php echo $term->slug; ?>" data-taxonomy="locations" class="taxonomy__term JS-Donor-Term" href="javascript:void(0)"><i class="taxonomy__term-circle fas fa-circle"></i></i><?php echo $term->name; ?><span class="taxonomy__term-count"><?php echo $term->count; ?></span></a>
                                    <?php }
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="filter__row JS-Hover">
                            <div class="btn filter__collapse JS-Collapse collapsed">
                                <div class="filter__collapse-text">Categories</div>
                                <button class="btn btn-view-all JS-Donor-ViewAll" data-taxonomy="categories" type="button"><i class="ico far fa-redo-alt"></i> View All</button>
                            </div>
                            <div class="collapse filter__collapse-body" id="collapseCategories">
                                <?php
                                $org_category = get_terms( 'org_category' );
                                if( $org_category && ! is_wp_error($org_category) ){
                                    echo '<div class="taxonomy d-flex flex-wrap align-content-start">';
                                    foreach( $org_category as $term ) {
                                        $term_meta = get_option( "taxonomy_$term->term_id" );
                                        if( isset($term_meta['exclude_filter']) && $term_meta['exclude_filter'] == '1' ) {
                                            continue;
                                        }
                                        ?>
                                        <a data-slug="<?php echo $term->slug; ?>" data-taxonomy="categories" class="taxonomy__term JS-Donor-Term" href="javascript:void(0)"><?php echo ( !empty($term_meta['fontawesome']) ) ? '<i class="taxonomy__term-icon '. $term_meta['fontawesome'] .'"></i>' : ''; echo $term->name; ?></a>
                                    <?php }
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="org-list">
    <div class="container p-0 w-100">
        <div class="row JS-Donor-Content">
            <?php
            global $wp_query;
            $wp_query = new WP_Query(array(
                'post_type' => 'organization',
                'posts_per_page' => '6',
                'paged' => get_query_var('paged') ? : 1,
                'orderby' => 'rand',
                'order' => 'ASC'
            ));
            while( have_posts() ){ the_post();
                ?>
                <div class="col-12 col-md-6 col-lg-4 pb-5">
                    <div class="org-list__item">
                        <?php echo get_post_meta( get_the_ID(), 'fundraiseup_url', true ); ?>
                    </div>
                </div>
                <?php
            }
            wp_reset_query();
            ?>
        </div>
        <div class="row">
            <button class="btn load-more JS-Donor-LoadMore" type="button" data-page="1">Load More</button>
        </div>
    </div>
</div>

