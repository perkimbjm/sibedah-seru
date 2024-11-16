document.addEventListener("DOMContentLoaded", function () {
    const mapCenter = [-2.3167, 115.3667];
    const mapZoom = 11.5;

    let map = new L.map(document.getElementById("map"), {
        zoom: mapZoom,
        center: mapCenter,
        fullscreenControl: {
            pseudoFullscreen: true, // if true, fullscreen to page width and height
        },
        layers: [osm],
    });

    // Memastikan peta diperbarui setelah dimuat sepenuhnya
    setTimeout(function () {
        map.invalidateSize();
    }, 1000);

    /*Zoom Extends*/
    L.easyButton({
        states: [
            {
                stateName: "zoom-to-default",
                icon: "fa-home fa-lg",
                title: "zoom to Balangan",
                onClick: function (btn, map) {
                    map.setView(mapCenter, mapZoom);
                    btn.state("zoom-to-default");
                },
            },
        ],
    }).addTo(map);

    /* GPS enabled geolocation control set to follow the user's location */
    const locateControl = L.control
        .locate({
            position: "topleft",
            drawCircle: true,
            follow: true,
            setView: true,
            keepCurrentZoomLevel: true,
            markerStyle: {
                weight: 1,
                opacity: 0.8,
                fillOpacity: 0.8,
            },
            circleStyle: {
                weight: 0.8,
                clickable: true,
            },
            icon: "fa fa-location-arrow",
            metric: true,
            strings: {
                title: "Lokasiku",
                outsideMapBoundsMsg:
                    "Kamu tampaknya berada di luar jangkauan peta",
            },
            locateOptions: {
                maxZoom: 18,
                watch: true,
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 10000,
            },
        })
        .addTo(map);

    // map.on("locationfound", function (e) {
    //     const latitude = e.latitude.toFixed(6);
    //     const longitude = e.longitude.toFixed(6);

    //     // Membuat popup dengan koordinat lokasi
    //     L.popup()
    //         .setLatLng([e.latitude, e.longitude])
    //         .setContent(`Koordinatmu: ${latitude}, ${longitude}`)
    //         .openOn(map);
    // });

    /*Scale Map*/
    L.control.scale({ imperial: false }).addTo(map);

    /*Basemap*/
    const iconLayers = [
        { title: "Positron", layer: cartoLight, icon: baseMapIcons.greyscale },
        { title: "Dark", layer: cartoDark, icon: baseMapIcons.dark },
        {
            title: "Voyager",
            layer: cartoVoyager,
            icon: baseMapIcons.cartoVoyager,
        },
        { title: "RBI", layer: rbi, icon: baseMapIcons.rbi },
        {
            title: "Google Maps",
            layer: googleMaps,
            icon: baseMapIcons.googleMaps,
        },
        {
            title: "Google Imagery",
            layer: googleSatellite,
            icon: baseMapIcons.googleSatellite,
        },
        { title: "OSM", layer: osm, icon: baseMapIcons.streetMap },
        { title: "Topo Map", layer: otopomap, icon: baseMapIcons.topoMap },
    ];

    let iconLayersControl = new L.Control.IconLayers(iconLayers, {
        position: "bottomright",
        maxLayersInRow: 4,
    });

    iconLayersControl.addTo(map);

    // define drawtoolbar options
    let options = {
        position: "topleft",
        drawMarker: true,
        drawPolyline: true,
        drawRectangle: true,
        drawPolygon: true,
        drawCircle: true,
        cutPolygon: true,
        editMode: true,
        removalMode: true,
    };

    // 3. Event listener for when a new layer is created
    // map.on("pm:create", function (e) {
    //     let jsn = e.layer.toGeoJSON().geometry;
    //     $.ajax({
    //         url: "resources/php_affected_constraints.php",
    //         data: { id: "geojson", geojson: JSON.stringify(jsn) },
    //         type: "POST",
    //         success: function (response) {
    //             $("#tableData").html(response);
    //             $("#dlgModal").show();
    //         },
    //         error: function (xhr, status, error) {
    //             $("#tableData").html("ERROR: " + error);
    //             $("#dlgModal").show();
    //         },
    //     });
    // });

    // Buat custom control untuk tombol utama
    L.easyButton({
        states: [
            {
                stateName: "draw",
                icon: "fa fa-wrench fa-lg",
                title: "draw this map",
                onClick: function (btn, map) {
                    togglePMToolbar();
                },
            },
        ],
    }).addTo(map);

    // Fungsi untuk menampilkan atau menyembunyikan tombol-tombol leaflet.pm
    function togglePMToolbar() {
        let pmToolbar = document.querySelector(".leaflet-pm-toolbar");
        if (pmToolbar) {
            if (
                pmToolbar.style.display === "none" ||
                pmToolbar.style.display === ""
            ) {
                pmToolbar.style.display = "block";
            } else {
                pmToolbar.style.display = "none";
            }
        }
    }

    // Aktifkan toolbar leaflet.pm dan sembunyikan saat pertama kali peta dimuat
    map.pm.addControls({
        position: "topleft",
        drawCircle: false,
        // Anda bisa mengatur opsi leaflet.pm lainnya di sini...
    });

    let pmToolbar = document.querySelector(".leaflet-pm-toolbar");
    if (pmToolbar) {
        pmToolbar.style.display = "none";
    }

    let ctlMeasure = L.control
        .polylineMeasure({ position: "topleft" })
        .addTo(map);

    const sidepanelLeft = L.control
        .sidepanel("mySidepanelLeft", {
            tabsPosition: "left",
            startTab: "tab-5",
        })
        .addTo(map);
    const sidepanelRight = L.control
        .sidepanel("mySidepanelRight", {
            panelPosition: "right",
            tabsPosition: "top",
            pushControls: true,
            darkMode: true,
            startTab: 2,
        })
        .addTo(map);
});
