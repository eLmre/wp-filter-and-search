(function ( $ ) {
    'use strict';

    var donor = donor || {};
    
    donor.init = function() {
        donor.$loadMore = jQuery('.JS-Donor-LoadMore');
        donor.$form = jQuery('.JS-Donor-Form');
        donor.$content = jQuery('.JS-Donor-Content');
        donor.$searchInput = jQuery('.JS-Donor-Search');
        donor.$searchBtn = jQuery('.JS-Donor-SearchBtn');
        donor.$taxonomyInput = jQuery('.JS-Donor-Taxonomy');
        donor.$viewAll = jQuery('.JS-Donor-ViewAll');
        donor.$term = jQuery('.JS-Donor-Term');

        this.preventFormSubmit();
        this.launchController();
        this.onLoadMore();
        //this.onSearchInput();
        this.onSearchBtn();
        this.onViewAll();
        //this.onTerm(); // multi-select
        this.onSingleTerm(); // single term only
        this.viewAllState();
        this.autoCompleteSearch();
        this.initGoogleMap();
    };

    donor.preventFormSubmit = function() {
        donor.$form.on('submit', function(e) {
            e.preventDefault();
            donor.$searchBtn.trigger('click');
        });
    };

    donor.launchController = function() {

        var taxonomyValue = donor.$taxonomyInput.map(function(){
            return $(this).val();
        }).get();

        if( taxonomyValue !== '' ) {

            $.each(taxonomyValue, function( index, value ) {

                var tags = value.split(",");

                $.each(tags, function( index, value ) {

                    var $tag = donor.$form.find("[data-slug='"+ value +"']");

                    if ( $tag.length ) {
                        $tag.addClass('active');
                    }

                });

            });

        }

        if(
            donor.$searchInput.val() !== '' || taxonomyValue !== ''
        ) {
            donor.$content.empty();
            var data = donor.collectData();
            donor.sendQuery(data);
        }

    };

    donor.filterState = function(data) {
        var url = location.protocol + '//' + location.host + location.pathname;
        var params = {};

        if( data.locations.length > 0 ) {
            params.locations = data.locations;
        }

        if( data.categories.length > 0 ) {
            params.categories = data.categories;
        }

        /**
         * Don't save search param into URL
         */
        // if( data.search.length > 0 ) {
        //     params.search = data.search;
        // }

        url = url + '?' + $.param(params);

        window.history.pushState('{}', document.title, url);
    };

    donor.collectData = function() {
        var data = {},
            form = donor.$form.serializeArray();

        $.each(form, function(i, field) {
            data[field.name] = field.value;
        });

        data.page = donor.$loadMore.data('page');
        data.nonce = global.nonce;
        data.action = 'donor_action';

        return data;
    };

    donor.sendQuery = function(data) {
        $.ajax({
            type: "post",
            url: global.ajaxurl,
            data: data,
            dataType: "json",
            beforeSend: function() {
                donor.filterState(data);
            },
            success: function(response) {
                if(response.success) {
                    if( response.data.html.length > 0 ) {
                        donor.addContent( response.data.html );
                    }
                    donor.loadMoreState( response.data.max_num_pages );
                    donor.btnState(donor.$searchBtn, 'on');
                    donor.$term.prop('disabled', false);
                }
            },
        });
    };

    donor.onLoadMore = function() {

        if ( !donor.$loadMore.length ) {
            return;
        }

        donor.$loadMore.on( 'click', function(e) {
            e.preventDefault();
            jQuery(this).addClass('active');
            jQuery(this).prop('disabled', true);
            var page = donor.$loadMore.data('page');
            donor.$loadMore.data('page', page + 1);
            var data = donor.collectData();
            donor.sendQuery(data);
        });

    };

    donor.loadMoreState = function(max_num_pages) {

        donor.$loadMore.removeClass('active');

        var dataPage = donor.$loadMore.data('page');

        if( dataPage < max_num_pages ) {
            donor.$loadMore.prop('disabled', false);
            donor.$loadMore.show();
        } else {
            donor.$loadMore.hide();
        }

    };

    donor.addContent = function(html) {
        donor.$content.append(html);
    };

    donor.onSearchInput = function() {
        var timeout = null;

        donor.$searchInput.on('keyup', function() {

            if( timeout != null ) {
                clearTimeout(timeout);
            }

            timeout = setTimeout(function() {
                donor.$loadMore.data('page', 1);
                donor.$content.empty();
                var data = donor.collectData();
                donor.sendQuery(data);
            }, 1000);

        });
    };

    donor.onSearchBtn = function() {
        if ( !donor.$searchBtn.length ) {
            return;
        }
        donor.$searchBtn.on('click', function(e) {
            e.preventDefault();
            donor.btnState(donor.$searchBtn, 'off');
            donor.$loadMore.data('page', 1);
            donor.$content.empty();
            var data = donor.collectData();
            donor.sendQuery(data);
        });
    };

    donor.btnState = function(element, status) {

        if( status === 'on' ) {
            element.removeClass('active');
            element.prop('disabled', false);
        } else if ( status === 'off' || '' ) {
            element.addClass('active');
            element.prop('disabled', true);
        }

    };

    donor.onViewAll = function() {

        if ( !donor.$viewAll.length ) {
            return;
        }

        donor.$viewAll.on('click', function(e) {
            e.preventDefault();

            var taxonomy = jQuery(this).data('taxonomy');
            donor.$term.filter("[data-taxonomy='"+ taxonomy +"']").removeClass('active');
            donor.$taxonomyInput.filter("[name='"+ taxonomy +"']").val('');
            donor.$loadMore.data('page', 1);
            donor.$searchInput.val('');
            donor.$viewAll.filter("[data-taxonomy='"+ taxonomy +"']").prop('disabled', true).removeClass('active');
            donor.$content.empty();
            var data = donor.collectData();
            donor.sendQuery(data);
        });

    };

    donor.viewAllState = function() {

        if ( !donor.$viewAll.length ) {
            return;
        }

        var taxonomies = donor.$viewAll.map(function(){
            return jQuery(this).data('taxonomy');
        }).get();

        if( taxonomies !== '' ) {

            $.each(taxonomies, function( index, taxonomy ) {

                var $activeTerm = donor.$term.filter(".active[data-taxonomy='"+ taxonomy +"']");
                var $btn = donor.$viewAll.filter("[data-taxonomy='"+ taxonomy +"']");

                if( !$activeTerm.length ) {
                    $btn.removeClass('active').prop('disabled', true);
                } else {
                    $btn.addClass('active').prop('disabled', false);
                }

            });

        }

    };

    donor.onTerm = function() {

        if ( !donor.$term.length ) {
            return;
        }

        donor.$term.on('click', function(e) {
            e.preventDefault();

            if( jQuery(this).hasClass('active') ) {
                jQuery(this).removeClass('active');
            } else {
                jQuery(this).addClass('active');
            }

            var termTaxonomy = jQuery(this).data('taxonomy');

            var $taxnomyInput = donor.$taxonomyInput.filter("input[name='"+ termTaxonomy +"']");

            var activeTerms = donor.$term.filter(".active[data-taxonomy='"+ termTaxonomy +"']");

            var taxnomyInputValue = '';

            activeTerms.each(function( index ) {
                if ( index === 0 ) {
                    taxnomyInputValue = $(this).data('slug');
                } else {
                    taxnomyInputValue = taxnomyInputValue + ',' + $(this).data('slug');
                }
            });

            $taxnomyInput.val(taxnomyInputValue);
            donor.$loadMore.data('page', 1);
            donor.$term.prop('disabled', true);
            donor.$content.empty();
            var data = donor.collectData();
            donor.sendQuery(data);
            donor.viewAllState();
        });
    };

    donor.onSingleTerm = function() {

        if ( !donor.$term.length ) {
            return;
        }

        donor.$term.on('click', function(e) {
            e.preventDefault();

            var termTaxonomy = jQuery(this).data('taxonomy');
            var $activeTerms = donor.$term.filter(".active[data-taxonomy='"+ termTaxonomy +"']");
            var $taxnomyInput = donor.$taxonomyInput.filter("input[name='"+ termTaxonomy +"']");

            if( jQuery(this).hasClass('active') ) {
                $activeTerms.removeClass('active');
                $taxnomyInput.val('');
            } else {
                $activeTerms.removeClass('active');
                jQuery(this).addClass('active');
                $taxnomyInput.val( $(this).data('slug') );
            }

            donor.$loadMore.data('page', 1);
            donor.$term.prop('disabled', true);
            donor.$content.empty();
            var data = donor.collectData();
            donor.sendQuery(data);
            donor.viewAllState();
        });
    };

    donor.autoCompleteSearch = function() {
        var searchRequest;

        donor.$searchInput.autoComplete({
            minChars: 2,
            onSelect: function(event, term, item){
                if( item.length ){
                    jQuery(this).val(item);
                }

                donor.$searchBtn.trigger('click');
            },
            source: function(term, suggest) {
                try { searchRequest.abort(); } catch(e){}
                searchRequest = jQuery.post(global.ajaxurl, {
                    search: term,
                    action: 'donor_search',
                    nonce: global.nonce
                }, function(response) {
                    suggest(response.data);
                });
            }
        });
    };

    donor.initGoogleMap = function() {
        if( jQuery('.JS-Donor-Map').length === 0 || global.google_map_status === "0" ) {
            return;
        }

        var args = {
            zoom: 7,
            center: new google.maps.LatLng(0, 0),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false,
            mapTypeControl: false,
            mapTypeControlOptions: {
                mapTypeIds: [google.maps.MapTypeId.ROADMAP]
            },
            styles: [
                {
                "elementType": "geometry",
                "stylers": [{
                    "color": "#ab7942"
                }]
            }, {
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#ffffff"
                }]
            }, {
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "administrative",
                "elementType": "geometry.fill",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "administrative.country",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#4b6878"
                }]
            }, {
                "featureType": "administrative.land_parcel",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "administrative.land_parcel",
                "elementType": "labels",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "administrative.locality",
                "elementType": "labels",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "administrative.neighborhood",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "administrative.neighborhood",
                "elementType": "labels",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "administrative.province",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#4b6878"
                }]
            }, {
                "featureType": "landscape.man_made",
                "elementType": "geometry",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "landscape.natural",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#84bc00"
                }]
            }, {
                "featureType": "landscape.natural.landcover",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#84bc00"
                }]
            }, {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#007395"
                }, {
                    "visibility": "off"
                }]
            }, {
                "featureType": "poi",
                "elementType": "labels",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "poi",
                "elementType": "labels.text",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#6f9ba5"
                }]
            }, {
                "featureType": "poi",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#1d2c4d"
                }]
            }, {
                "featureType": "poi.business",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "poi.business",
                "elementType": "labels",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "poi.park",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#023e58"
                }, {
                    "visibility": "off"
                }]
            }, {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#3C7680"
                }]
            }, {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#304a7d"
                }]
            }, {
                "featureType": "road",
                "elementType": "labels",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "road",
                "elementType": "labels.icon",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#98a5be"
                }, {
                    "visibility": "off"
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#a8d440"
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#255763"
                }, {
                    "visibility": "off"
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#b0d5ce"
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "color": "#023e58"
                }]
            }, {
                "featureType": "transit",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "transit",
                "elementType": "labels",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "transit.line",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#283d6a"
                }]
            }, {
                "featureType": "transit.station",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#3a4762"
                }, {
                    "visibility": "off"
                }]
            }, {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#007395"
                }]
            }, {
                "featureType": "water",
                "elementType": "labels.text",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#ebebeb"
                }]
            }
            ]
        };
        var map = new google.maps.Map( jQuery('.JS-Donor-Map')[0], args );

        map.markers = [];
        var $markers = jQuery(document).find('.js-marker');
        var index = 0;
        $markers.each(function() {
            donor.initMapMarker( $(this), map, index );
            index++;
        });

        donor.centerMap( map );
    };

    donor.initMapMarker = function( $marker, map, index ) {
        var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') ),
            defaultIcon = "https://mt.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png",
            activeIcon = "https://mt.google.com/vt/icon?color=ff004C13&name=icons/spotlight/spotlight-waypoint-blue.png",
            initIcon = defaultIcon;

        if( $marker.hasClass('active') ) {
            initIcon = activeIcon;
        }

        // create marker
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            slug: $marker.data('slug'),
            icon: initIcon,
        });

        // add to array
        map.markers.push( marker );

        // if marker contains HTML, add it to an infoWindow
        if( $marker.html() ) {

            // create info window, put html from tooltip div
            var infowindow = new google.maps.InfoWindow({
                content: $marker.find('.js-marker-tooltip').html()
            });

            // Click on term button from "locations" tab
            jQuery(".JS-Donor-Term[data-slug='"+ marker.slug +"']").on('click', function(){
                for (var j = 0; j < map.markers.length; j++) {
                    map.markers[j].setIcon(defaultIcon);
                }
                marker.setIcon(activeIcon);
            });

            // View All button
            jQuery(".JS-Donor-ViewAll[data-taxonomy=\"locations\"]").on('click', function(){
                for (var j = 0; j < map.markers.length; j++) {
                    map.markers[j].setIcon(defaultIcon);
                }
            });

            // Direct click on marker
            google.maps.event.addListener(marker, 'click', function() {
                for (var j = 0; j < map.markers.length; j++) {
                    map.markers[j].setIcon(defaultIcon);
                }
                this.setIcon(activeIcon);
                jQuery('.JS-Donor-Term[data-slug="'+ marker.slug +'"]').trigger('click');
            });

            marker.addListener('mouseover', function() {
                infowindow.open(map, this);
            });

            marker.addListener('mouseout', function() {
                infowindow.close(map, this);
            });

        }

    };

    donor.centerMap = function( map ) {
        // vars
        var bounds = new google.maps.LatLngBounds();

        // loop through all markers and create bounds
        $.each( map.markers, function( i, marker ){
            var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
            bounds.extend( latlng );
        });

        // only 1 marker?
        if( map.markers.length == 1 ) {
            // set center of map
            //alert(bounds);
            map.setCenter( bounds.getCenter() );
            map.setZoom( 16 );
        } else {
            // fit to bounds
            map.fitBounds(bounds);
            var listener = google.maps.event.addListener(map, "idle", function() {
                map.setZoom(3);
                google.maps.event.removeListener(listener);
            });
        }

    };

    /**
     * Document ready
     */
    $( function () {
        donor.init();
    } );

    $( function () {
        jQuery(".JS-Collapse").each(function() {
            var $this = jQuery(this),
                $button = $this.next('.collapse'),
                $toggle = $this.find('.JS-Collapse-Toggle');

            $this.on('click', function () {
                $button.collapse('toggle');
            });

            $button.on('show.bs.collapse', function () {
                $this.removeClass('collapsed');
            });

            $button.on('hide.bs.collapse', function () {
                $this.addClass('collapsed');
            });
        });
    } );

    $( function () {
        $(".JS-Hover").mouseenter(function() {
            $( this ).addClass('hovered');
        }).mouseleave(function() {
            $( this ).removeClass('hovered');
        });
    } );

})( jQuery );





