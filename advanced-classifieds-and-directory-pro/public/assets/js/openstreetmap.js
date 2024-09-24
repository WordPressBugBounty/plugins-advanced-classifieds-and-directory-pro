'use strict';

(function( $ ) {

	/**
     * Init map.
	 */
	const initMap = () => {
		document.querySelectorAll( '.acadp-map:not(.acadp-map-loaded)' ).forEach(( mapEl ) => {		
			createMap( mapEl );
		});
	}

	/**
     * Create map.
	 */
	const createMap = ( mapEl ) => {
		mapEl.classList.add( 'acadp-map-loaded' );

		// Vars
		const markersEl = mapEl.querySelectorAll( '.marker' );		
		const type = mapEl.dataset.type;

		let latitude  = 0;
		let longitude = 0;
		let popupContent = '';

		if ( markersEl.length > 0 ) {
			latitude     = markersEl[0].dataset.latitude;
			longitude    = markersEl[0].dataset.longitude;
			popupContent = markersEl[0].innerHTML;
		}

		// Set a custom image path.
		L.Icon.Default.prototype.options.imagePath = acadp.plugin_url + 'vendor/leaflet/images/';

		// Creating map options.
		const mapOptions = {
			center: [ latitude, longitude ],
			zoom: acadp.zoom_level
		}

		// Creating a map object.        	
		let map = new L.map( mapEl, mapOptions );	

		// Creating a Layer object.
		const layer = new L.TileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		});

		// Adding layer to the map.
		map.addLayer( layer );

		if ( type === 'archive' ) {
			// Creating marker options.
			const markerOptions = {
				clickable: true,
				draggable: false
			}

			// Creating markers.	
			let markers = L.markerClusterGroup();

			markersEl.forEach(( markerEl ) => {	
				const latitude = markerEl.dataset.latitude;
				const longitude = markerEl.dataset.longitude;

				// Creating a marker.
				const marker = L.marker( [ latitude, longitude ], markerOptions );

				// Adding popup to the marker.
				const content = markerEl.innerHTML;
				if ( content ) {
					marker.bindPopup( content, { minWidth: 280, maxHeight: 200 } );
				}

				markers.addLayer( marker );
			});

			map.addLayer( markers );

			// Center map.
			if ( acadp.snap_to_user_location && navigator.geolocation ) {
				// Try HTML5 geolocation.
				navigator.geolocation.getCurrentPosition(function( position ) {
				  map.panTo( new L.LatLng( position.coords.latitude, position.coords.longitude ) );
				}, function() {
					// Browser doesn't support Geolocation.
					map.fitBounds(markers.getBounds(), {
						padding: [50, 50]
					});
				});
			} else {
				map.fitBounds(markers.getBounds(), {
					padding: [50, 50]
				});	
			}
		} else {
			// Creating marker options.
			const markerOptions = {
				clickable: true,
				draggable: ( type === 'form' ? true : false )
			}

			// Creating a marker.
			let marker = L.marker( [ latitude, longitude ], markerOptions );

			// Adding popup to the marker.
			if ( popupContent ) {
				marker.bindPopup( popupContent, { minWidth: 280, maxHeight: 200 } );
			}

			// Adding marker to the map.
			marker.addTo( map );

			// Is the map editable?
			if ( type === 'form' ) {				
				// Update latitude and longitude values in the form when marker is moved
				marker.addEventListener( 'dragend', function( event ) {
					const position = event.target.getLatLng();

					map.panTo( new L.LatLng( position.lat, position.lng ) );
					updateLatLng( position.lat, position.lng );
				});

				// Update map when the contact fields are edited.
				const onAddressChange = () => {
					let query = [];					

					const $locationEl = $( '#acadp-form-control-location' );
					let location = '';

					if ( $locationEl.is( 'select' ) ) {
						const $selectedOption = $locationEl.find( 'option:selected' );

						if ( $selectedOption && $selectedOption.val() > 0 ) {   
							location = $selectedOption.text().trim();
							query.push( location );

							// Find the current level number
							let classNames = $selectedOption.attr( 'class' );
							classNames = classNames.split( ' ' );

							let level = 0;

							for ( let i = 0; i < classNames.length; i++ ) {
								let className = classNames[ i ].trim();

								if ( className.indexOf( 'level-' ) !== -1 ) {
									level = parseInt( className.split( 'level-' )[1] );
									break;
								}
							}

							// Loop through all levels to find the parent location names
							let $previousEl = $selectedOption.prev();

							for ( let i = level - 1; i >= 0; i-- ) {
								while ( ! $previousEl.hasClass( 'level-' + i ) ) {						
									$previousEl = $previousEl.prev();					
								}

								location = $previousEl.text().trim();
								query.push( location );
							}								
						}
					} else {
						const $selectedOption = $locationEl.find( 'input[name=acadp_location]:checked' );

						if ( $selectedOption && $selectedOption.val() > 0 ) {   
							let $parentLocations = $selectedOption.parents( '.acadp-term' );

							$parentLocations.each(function() {
								location = $( this ).find( '.acadp-term-name' ).html().trim();
								query.push( location );
							});
						}	
					}	

					const zipcode = $( '#acadp-form-control-zipcode' ).val();
					if ( zipcode ) {
						if ( location ) {
							query = [ location, zipcode ]; // Bind only the top-level location (country)
						} else {
							query.push( zipcode );
						}
					}

					if ( 0 == query.length ) {
						let address = $( '#acadp-form-control-address' ).val();

						if ( address ) {
							address = address.trim();
							address = address.replace( /(?:\r\n|\r|\n)/g, ',' );					
							address = address.replaceAll( ', ', ',' );
							address = address.replaceAll( ' ,', ',' );	
							address = address.replaceAll( ',,', ',' );

							query.push( address );
						}
					}

					query = query.filter( function( v ) { return v !== '' } );
					query = query.join();
					
					$.get( 'https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent( query ) +'&format=jsonv2&limit=1', function( response ) {
						if ( response.length > 0 ) {
							const latLng = new L.LatLng( response[0].lat, response[0].lon );

							marker.setLatLng( latLng );
							map.panTo( latLng );
							updateLatLng( response[0].lat, response[0].lon );
						}
					}, 'json' );
				}

				$( '#acadp-panel-contact-details' ).on( 'blur', '.acadp-form-control-map', function() {
					onAddressChange();
				});

				if ( ! latitude || ! longitude ) {
					onAddressChange();
				}
			}
		}	
	}
	
	/**
	 * Update the latitude and longitude field values.
	 */
	const updateLatLng = ( latitude, longitude ) => {		
		$( '#acadp-form-control-latitude' ).val( latitude );
		$( '#acadp-form-control-longitude' ).val( longitude );
	}	
	
	/**
	 * Called when the page has loaded.
	 */
	$(function() {	

		// Init map.
		if ( acadp.show_cookie_consent ) {
			document.addEventListener( 'acadp.cookie.consent', initMap );
		} else {
			initMap();			
		}
		
		// Gutenberg: Load map.
		if ( 'undefined' !== typeof wp && 'undefined' !== typeof wp['hooks'] ) {
			let intervalHandler;
			let retryCount;

			const loadMapBlock = () => {
				if ( retryCount > 0 ) {
					clearInterval( intervalHandler );
				}	

				retryCount = 0;

				intervalHandler = setInterval(() => {
					retryCount++;

					if ( document.querySelectorAll( '.acadp-map:not(.acadp-map-loaded)' ) !== null || retryCount >= 10 ) {
						clearInterval( intervalHandler );
						retryCount = 0;

						initMap();
					}
				}, 1000	);
			}

			wp.hooks.addAction( 'acadp_init_listings', 'acadp/listings', function( attributes ) {
				if ( 'map' === attributes.view ) {
					loadMapBlock();
				}
			});

			wp.hooks.addAction( 'acadp_init_listing_form', 'acadp/listing-form', function() {
				loadMapBlock();
			});
		}
		
	});

})( jQuery );
