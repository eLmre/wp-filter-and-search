<div class="donation-hero"></div>

<div class="container p-0 w-100 JS-Donor">
    <div class="row no-gutters">
        <div class="col-12 col-lg-7 mx-auto">
        <div class="donor-title">
            <h1 class="donor-title__text">Support your favorite organizations quickly and easily!</h1>
        </div>
    </div>
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

                    <div class="filter__row JS-Hover">
                        <div class="btn filter__collapse JS-Collapse collapsed">
                            <div class="filter__collapse-text JS-Collapse-Text">Locations</div>
                            <button class="btn btn-view-all JS-Donor-ViewAll" data-taxonomy="locations" type="button"><i class="ico far fa-redo-alt"></i> View All</button>
                            <a class="filter__collapse-overlay collapsed" data-toggle="collapse" data-target="#collapseLocations" aria-expanded="false" aria-controls="collapseLocations"></a>
                        </div>
                        <div class="collapse" id="collapseLocations">
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
                            <div class="filter__collapse-text JS-Collapse-Text">Categories</div>
                            <button class="btn btn-view-all JS-Donor-ViewAll" data-taxonomy="categories" type="button"><i class="ico far fa-redo-alt"></i> View All</button>
                            <a class="filter__collapse-overlay collapsed" data-toggle="collapse" data-target="#collapseCategories" aria-expanded="false" aria-controls="collapseCategories"></a>
                        </div>
                        <div class="collapse" id="collapseCategories">
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

<div class="spec-info">
    <div class="container p-0 w-100">
        <h2 class="spec-info__title">Together We Can Make a Difference.</h2>
        <div class="d-flex flex-wrap justify-content-center">
        <div class="spec-info__block spec-info__block_alt col-12 col-lg-5 align-content-stretch">
            <div class="spec-info__block-inner">

            </div>
        </div>
        <div class="spec-info__block spec-info__block_alt mt-5 mt-lg-0 col-12 col-lg-5 align-content-stretch">
            <div class="spec-info__block-inner">
                easy to support our mission online, with options that work for you.
            </div>
        </div>
        <div class="spec-info__block col-12 col-lg-6 align-content-start">
            <div class="spec-info__block-inner">
                Please contact our team for more information
            </div>
        </div>
    </div>
    </div>
</div>

<div class="cityscape-footer">
    <div class="container w-100">
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="cityscape-footer__col">
                    <p></p>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="cityscape-footer__col">
                    <div style="text-align: center;" class="fr-tag"><br></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="cityscape-footer__col">
                    <p class="fr-tag"><span class="far fa-copyright"></span> Nonprofit registered in the US under EIN:. All donations are tax deductible.</p>
                </div>
            </div>
            <div class="col-md-12 clearfix cityscape-footer__copyright">
                <span class="far fa-copyright"></span>
                2019 ALL RIGHTS RESERVED
            </div>
        </div>
    </div>
</div>
