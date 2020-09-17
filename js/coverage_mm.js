/**
 * coverage_mm js
 * felismeri, hogy a kategória felvivó/modositó képernyő van betöltve.
 * ha igen akkor az isarea beállítástól függően mezőket rejt el/jelenít meg, map -et hoz létre, jelenít meg
 * a térékép paramétereit a "center_lat", "center_lng", "map_zoom" mezők tartalmazzák.
 * Új felvitelnél a globális "defMap" objektumból veszi a térkép értékeket.
 * defMap tartalma: {lat:num, lng:num, zoom:num}
 */
jQuery(function() {
	// check: it is product_cat form?
	if (jQuery('.acf-field-number[data-name="population"]').length > 0) {

		// include leaflet map in form 
		jQuery('<div id="mapContainer" style="width:600px; height:400px"><div id="map" style="width: 100%; height:100%"></div></div>')
		.insertAfter(jQuery('.acf-field[data-name="isarea"] .acf-input label'));
		
		var mymap = false;
		var lat = jQuery('.acf-field[data-name=center_lat] input').val();
		var lng = jQuery('.acf-field[data-name=center_lng] input').val();
		var zoom = jQuery('.acf-field[data-name=map_zoom] input').val();
		if ((lat == '') | (lat == undefined) | (isNaN(lat))) {
			lat = defMap.lat;
		}
		if ((lng == '') | (lng == undefined) | (isNaN(lng))) {
			lng = defMap.lng;
		}
		if ((zoom == '') | (zoom == undefined) | (isNaN(zoom))) {
			zoom = defMap.zoom;
		}
		jQuery('.acf-field[data-name=center_lat] input').val(lat);
		jQuery('.acf-field[data-name=center_lng] input').val(lng);
		jQuery('.acf-field[data-name=map_zoom] input').val(zoom);
		
		// ezek a mezők mindig rejtettek
		jQuery('.acf-field[data-name=map_id]').hide();
		jQuery('.acf-field[data-name=poligon]').hide();
		
		// isarea beállítástól függően mezők elrejtése vagy mutatása
		jQuery('.acf-field[data-name="isarea"] input').change(function() {
			var isarea = jQuery('.acf-field[data-name="isarea"] input').is(':checked');
			if (isarea) {
				jQuery('.acf-field[data-name=area_category]').show();
				jQuery('.acf-field[data-name=center_lat]').show();
				jQuery('.acf-field[data-name=center_lng]').show();
				jQuery('.acf-field[data-name=map_zoom]').show();
				jQuery('.acf-field[data-name=population]').show();
				jQuery('.acf-field[data-name=place]').show();
				jQuery('#mapContainer').show();
				// ha még nincs feéépítve a map akkor most létrehozzuk
				if (!mymap) {
					mymap = L.map('map').setView([lat, lng], zoom);
					L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					maxZoom: 18,
					attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
						'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
						'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
					id: 'mapbox/streets-v11',
					tileSize: 512,
					zoomOffset: -1
					}).addTo(mymap);
					mymap.on('moveend', function myHandler(e) {
						var latLng = mymap.getCenter();
						jQuery('.acf-field[data-name=center_lat] input').val(latLng.lat);
						jQuery('.acf-field[data-name=center_lng] input').val(latLng.lng);
					});
					mymap.on('zoomend', function myHandler(e) {
						var zoom = mymap.getZoom();
						jQuery('.acf-field[data-name=map_zoom] input').val(zoom);
					});
				}
			} else {
				jQuery('.acf-field[data-name=area_category]').hide();
				jQuery('.acf-field[data-name=center_lat]').hide();
				jQuery('.acf-field[data-name=center_lng]').hide();
				jQuery('.acf-field[data-name=map_zoom]').hide();
				jQuery('.acf-field[data-name=population]').hide();
				jQuery('.acf-field[data-name=place]').hide();
				jQuery('#mapContainer').hide();
			} // isarea?
		});
		// isarea alapján képernyő init
		jQuery('.acf-field[data-name="isarea"] input').change();
		
	} // product_cat formon vagyunk?		
});