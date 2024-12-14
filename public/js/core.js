setTimeout(function () {
    if (map) {
        map.invalidateSize();
    }
}, 1000);

window.DesaSearch = [];

const mapCenter = [-2.33668, 115.46028];
const mapZoom = 18;

/*Zoom Extends*/
if (L && L.easyButton) {
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
    console.log("Easy button berhasil dimuat");
}

if (L && L.Control && L.Control.geocoder) {
    L.Control.geocoder({
        position: "topleft",
        collapsed: true,
    }).addTo(map);
    console.log("Geocoder control berhasil dimuat");
}

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
            outsideMapBoundsMsg: "Kamu tampaknya berada di luar jangkauan peta",
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
console.log("Locate control berhasil dimuat");

/*Scale Map*/
if (L && L.control && L.control.scale) {
    L.control.scale({ imperial: false }).addTo(map);
    console.log("Scale control berhasil dimuat");
}

/*Basemap*/
const baseMapIcons = {
    greyscale: "/img/basemap/greyscale.png",
    rbi: "/img/basemap/rbi.png",
    googleMaps: "/img/basemap/googleMaps.png",
    googleSatellite: "/img/basemap/googleSatellite.png",
    streetMap: "/img/basemap/streetmap.png",
    topoMap: "/img/basemap/topomap.png",
    dark: "/img/basemap/dark.png",
    cartoVoyager: "/img/basemap/voyager.png",
};

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
console.log("Icon layers control berhasil dimuat");

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

if (L && L.easyButton) {
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
}

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
});
console.log("PM toolbar berhasil dimuat");

let pmToolbar = document.querySelector(".leaflet-pm-toolbar");
if (pmToolbar) {
    pmToolbar.style.display = "none";
}

let ctlMeasure = L.control.polylineMeasure({ position: "topleft" }).addTo(map);
console.log("Measure control berhasil dimuat");

function showModal(title, content) {
    document.getElementById("feature-title").innerText = title;
    document.getElementById("feature-info").innerHTML = content;
    const modal = document.getElementById("featureModal");
    if (modal) {
        modal.classList.remove("hidden");
    }
}

function zoomToFeature(e) {
    map.fitBounds(e.target.getBounds());
}

// Buat fungsi untuk memuat data kecamatan
let kecamatan = L.geoJson(null, {
    style: function (feature) {
        return {
            color: "#2F4F4F",
            fill: true,
            fillColor:
                {
                    Awayan: "#FFD700",
                    "Batu Mandi": "#008080",
                    Halong: "#FF6347",
                    Juai: "#008000",
                    Lampihong: "#FFA500",
                    Paringin: "#87CEEB",
                    "Paringin Selatan": "#FFFF00",
                    "Tebing Tinggi": "#CCFFCC",
                }[feature.properties.name] || "#FFFFFF",
            fillOpacity: 0.6,
            opacity: 0.5,
            width: 0.001,
            clickable: true,
        };
    },
    onEachFeature: function (feature, layer) {
        layer.on({
            mouseover: function (e) {
                let layer = e.target;
                layer.setStyle({
                    weight: 3,
                    color: "blue",
                    opacity: 1,
                });
            },
            mouseout: function (e) {
                kecamatan.resetStyle(e.target);
            },
            click: zoomToFeature,
        });
    },
});

let tooltipKecamatan = L.layerGroup();
let isKecamatanLoaded = false;

// Fungsi untuk memuat data kecamatan
function loadKecamatanData() {
    if (!isKecamatanLoaded) {
        fetch("/api/kecamatan/geojson")
            .then((response) => response.json())
            .then((data) => {
                L.geoJson(data, {
                    onEachFeature: function (feature, layer) {
                        let tooltip = L.tooltip({
                            permanent: true,
                            direction: "center",
                            className: "no-background",
                        })
                            .setContent(feature.properties.name)
                            .setLatLng(layer.getBounds().getCenter());

                        tooltipKecamatan.addLayer(tooltip);
                    },
                });
                kecamatan.addData(data);
                isKecamatanLoaded = true;
                console.log("Data kecamatan berhasil dimuat");
            })
            .catch((error) => {
                console.error("Error loading kecamatan data:", error);
            });
    }
}

// Buat fungsi untuk memuat data desa
let desa = L.geoJson(null, {
    style: function (feature) {
        return {
            color: "skyblue",
            fill: true,
            fillColor: "lightgrey",
            fillOpacity: 0.4,
            opacity: 0.85,
            width: 0.005,
            clickable: true,
        };
    },
    onEachFeature: function (feature, layer) {
        DesaSearch.push({
            name: layer.feature.properties.name,
            source: "desa",
            id: L.stamp(layer),
            bounds: layer.getBounds(),
        });
        layer.on({
            mouseover: function (e) {
                let layer = e.target;
                layer.setStyle({
                    weight: 3,
                    color: "blue",
                    opacity: 1,
                });
            },
            mouseout: function (e) {
                desa.resetStyle(e.target);
            },
            click: zoomToFeature,
        });
    },
});

let tooltipDesa = L.layerGroup();
let isDesaLoaded = false;

// Fungsi untuk memuat data desa
function loadDesaData() {
    if (!isDesaLoaded) {
        fetch("api/desa/geojson")
            .then((response) => response.json())
            .then((data) => {
                L.geoJson(data, {
                    onEachFeature: function (feature, layer) {
                        let tooltip = L.tooltip({
                            permanent: true,
                            direction: "center",
                            className: "no-background",
                        })
                            .setContent(feature.properties.name)
                            .setLatLng(layer.getBounds().getCenter());

                        tooltipDesa.addLayer(tooltip);
                    },
                });
                desa.addData(data);
                isDesaLoaded = true;
                console.log("Data desa berhasil dimuat");
            })
            .catch((error) => {
                console.error("Error loading desa data:", error);
            });
    }
}

// Buat fungsi untuk memuat data kumuh
let kumuh = L.geoJson(null, {
    style: function (feature) {
        return {
            color: "grey",
            fillColor: "magenta",
            fillOpacity: 0.5,
            opacity: 0.5,
            width: 0.001,
            clickable: true,
            title: feature.properties.KECAMATAN,
            riseOnHover: true,
        };
    },
    onEachFeature: function (feature, layer) {
        if (feature.properties) {
            let content =
                "<table class='table-auto w-full'>" +
                "<tr><th class='text-left'>LUASAN KUMUH</th><td>" +
                feature.properties.LUAS_ha +
                " Ha</td></tr>" +
                "<tr><th class='text-left'>LOKASI KUMUH</th><td>" +
                feature.properties.LOKASI_KUM +
                "</td></tr>" +
                "</table>";
            layer.on({
                click: function (e) {
                    showModal(feature.properties.KECAMATAN, content);
                },
            });
        }
        layer.on({
            mouseover: function (e) {
                let layer = e.target;
                layer.setStyle({
                    weight: 3,
                    color: "#00FFFF",
                    opacity: 1,
                });
                if (!L.Browser.ie && !L.Browser.opera) {
                    layer.bringToFront();
                }
            },
            mouseout: function (e) {
                kumuh.resetStyle(e.target);
            },
            click: zoomToFeature,
        });
    },
});

let isKumuhLoaded = false;

function loadKumuhData() {
    if (!isKumuhLoaded) {
        fetch("/data/KUMUH_AR.geojson")
            .then((response) => response.json())
            .then((data) => {
                kumuh.addData(data);
                isKumuhLoaded = true;
                console.log("Data kumuh berhasil dimuat");
            })
            .catch((error) => {
                console.error("Error loading kumuh data:", error);
            });
    }
}

let isPolaruangLoaded = false;
let isLayerVisible = false;

// Tambahkan opsi caching pada WMS layer
let polaruang = L.tileLayer.wms("/proxy/wms?", {
    layers: "simtaruBalangan:polaruang_rtrw",
    format: "image/png",
    transparent: true,
    version: "1.1.0",
    tileSize: 256,
    updateWhenIdle: true,
    updateWhenZooming: false,
    keepBuffer: 2,
    // Tambahkan parameter untuk membantu caching
    time: new Date().getTime(), // Timestamp untuk force cache
    cacheControl: "max-age=3600", // Cache selama 1 jam
    noCache: false,
});

// Tambahkan event listener untuk tile loading
if (polaruang) {
    polaruang.on("loading", function () {
        console.log("Memuat tiles...");
    });

    polaruang.on("load", function () {
        console.log("Tiles selesai dimuat");
    });
}

function loadPolaruangData() {
    if (!isPolaruangLoaded) {
        // Initial load
        if (map && polaruang) {
            map.addLayer(polaruang);
        }
        isPolaruangLoaded = true;
        isLayerVisible = true;
        console.log("Data polaruang berhasil dimuat pertama kali");
    } else if (!isLayerVisible) {
        // Reaktivasi layer
        if (map && polaruang) {
            map.addLayer(polaruang);
        }
        isLayerVisible = true;
        console.log("Polaruang diaktifkan kembali tanpa mengunduh ulang");
    }
}

const createClusterGroup = () => {
    return L.markerClusterGroup({
        chunkedLoading: true,
        chunkInterval: 200,
        chunkDelay: 50,
        maxClusterRadius: (zoom) => {
            return zoom <= 13 ? 80 : zoom <= 15 ? 40 : 20;
        },
        spiderfyOnMaxZoom: false,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true,
        removeOutsideVisibleBounds: true,
        animate: true,
        animateAddingMarkers: false,
        disableClusteringAtZoom: 16,
        maxZoom: 16,
    });
};

const isValidLatLng = (lat, lng) => {
    return (
        lat &&
        lng &&
        !isNaN(lat) &&
        !isNaN(lng) &&
        lat >= -90 &&
        lat <= 90 &&
        lng >= -180 &&
        lng <= 180
    );
};

const house = L.geoJson(null, {
    pointToLayer: function (feature, latlng) {
        if (!isValidLatLng(latlng.lat, latlng.lng)) {
            console.warn("Invalid coordinates detected:", latlng);
            return null;
        }

        const marker = L.marker(latlng, {
            icon: L.icon({
                iconUrl: "/img/home-blue.png",
                iconSize: [16, 16],
                iconAnchor: [8, 16],
                popupAnchor: [0, -16],
            }),
        });

        marker.options.clickable = true;
        marker.options.riseOnHover = true;
        marker.options.bubblingMouseEvents = false;

        return marker;
    },
    onEachFeature: function (feature, layer) {
        if (feature.properties) {
            const content = `<table class='table-auto w-full'>
                <tr><th class='text-left'>ID</th><td>${
                    feature.properties.id || "-"
                }</td></tr>
                <tr><th class='text-left'>Nama</th><td>${
                    feature.properties.name || "-"
                }</td></tr>
                <tr><th class='text-left'>Alamat</th><td>${
                    feature.properties.address || "-"
                }</td></tr>
                <tr><th class='text-left'>Kecamatan</th><td>${
                    feature.properties.district || "-"
                }</td></tr>
                <tr><th class='text-left'>Tahun</th><td>${
                    feature.properties.year || "-"
                }</td></tr>
                <tr><th class='text-left'>Tipe</th><td>${
                    feature.properties.type || "-"
                }</td></tr>
                <tr><th class='text-left'>Sumber</th><td>${
                    feature.properties.source || "-"
                }</td></tr>
                <tr><th class='text-left'>Catatan</th><td>${
                    feature.properties.note || "-"
                }</td></tr>
            </table>`;
            layer.bindPopup(content, {
                closeButton: true,
                autoPan: false,
                maxWidth: 300,
            });
        }
    },
    filter: function (feature) {
        const coords = feature.geometry.coordinates;
        return isValidLatLng(coords[1], coords[0]);
    },
});

const rtlh = L.geoJson(null, {
    pointToLayer: function (feature, latlng) {
        if (!isValidLatLng(latlng.lat, latlng.lng)) {
            console.warn("Invalid coordinates detected:", latlng);
            return null;
        }

        const marker = L.marker(latlng, {
            icon: L.icon({
                iconUrl: "/img/home-red.png",
                iconSize: [16, 16],
                iconAnchor: [8, 16],
                popupAnchor: [0, -16],
            }),
        });

        marker.options.clickable = true;
        marker.options.riseOnHover = true;
        marker.options.bubblingMouseEvents = false;

        return marker;
    },
    onEachFeature: function (feature, layer) {
        if (feature.properties) {
            const content = `<table class='table-auto w-full'>
                <tr><th class='text-left'>ID</th><td>${
                    feature.properties.id || "-"
                }</td></tr>
                <tr><th class='text-left'>Nama</th><td>${
                    feature.properties.name || "-"
                }</td></tr>
                <tr><th class='text-left'>Alamat</th><td>${
                    feature.properties.address || "-"
                }</td></tr>
                <tr><th class='text-left'>Jumlah Penghuni</th><td>${
                    feature.properties.people || "-"
                }</td></tr>
                <tr><th class='text-left'>Pondasi</th><td>${
                    feature.properties.pondasi || "-"
                }</td></tr>
                <tr><th class='text-left'>Atap</th><td>${
                    feature.properties.atap || "-"
                }</td></tr>
                <tr><th class='text-left'>Dinding</th><td>${
                    feature.properties.dinding || "-"
                }</td></tr>
                <tr><th class='text-left'>Lantai</th><td>${
                    feature.properties.lantai || "-"
                }</td></tr>
                <tr><th class='text-left'>Status</th><td>${
                    feature.properties.status || "-"
                }</td></tr>
                <tr><th class='text-left'>Catatan</th><td>${
                    feature.properties.note || "-"
                }</td></tr>
            </table>`;
            layer.bindPopup(content, {
                closeButton: true,
                autoPan: false,
                maxWidth: 300,
            });
        }
    },
    filter: function (feature) {
        const coords = feature.geometry.coordinates;
        return isValidLatLng(coords[1], coords[0]);
    },
});

const houseCluster = createClusterGroup();
const rtlhCluster = createClusterGroup();

let isHouseLoaded = false;
let isRtlhLoaded = false;
let activePopup = null;

async function loadHouseData() {
    if (!isHouseLoaded) {
        try {
            const response = await fetch("/api/bedah/general");
            const data = await response.json();

            if (!Array.isArray(data.data)) {
                throw new Error("Data yang diterima bukan array");
            }

            const validFeatures = data.data
                .filter((item) => isValidLatLng(item.lat, item.lng))
                .map((item) => ({
                    type: "Feature",
                    geometry: {
                        type: "Point",
                        coordinates: [item.lng, item.lat],
                    },
                    properties: {
                        id: item.id,
                        name: item.name,
                        address: item.address,
                        district: item.district.name,
                        year: item.year,
                        type: item.type,
                        source: item.source,
                        note: item.note,
                    },
                }));

            if (validFeatures.length > 0) {
                const geoJsonData = {
                    type: "FeatureCollection",
                    features: validFeatures,
                };

                house.clearLayers();
                house.addData(geoJsonData);

                houseCluster.clearLayers();
                houseCluster.addLayer(house);

                if (!map.hasLayer(houseCluster)) {
                    map.addLayer(houseCluster);
                    console.log("Layer houseCluster ditambahkan");
                }

                isHouseLoaded = true;
                console.log("Data house berhasil dimuat");
            }
        } catch (error) {
            console.error("Error loading house data:", error);
        }
    }
}

async function loadRtlhData() {
    if (!isRtlhLoaded) {
        try {
            const response = await fetch("/api/rtlh");
            const data = await response.json();

            if (!Array.isArray(data.data)) {
                throw new Error("Data yang diterima bukan array");
            }

            const validFeatures = data.data
                .filter((item) => isValidLatLng(item.lat, item.lng))
                .map((item) => ({
                    type: "Feature",
                    geometry: {
                        type: "Point",
                        coordinates: [item.lng, item.lat],
                    },
                    properties: {
                        id: item.id,
                        name: item.name,
                        address: item.address,
                        people: item.people,
                        pondasi: item.pondasi,
                        atap: item.atap,
                        dinding: item.dinding,
                        lantai: item.lantai,
                        status: item.status,
                        note: item.note,
                    },
                }));

            if (validFeatures.length > 0) {
                const geoJsonData = {
                    type: "FeatureCollection",
                    features: validFeatures,
                };

                rtlh.clearLayers();
                rtlh.addData(geoJsonData);

                rtlhCluster.clearLayers();
                rtlhCluster.addLayer(rtlh);

                if (!map.hasLayer(rtlhCluster)) {
                    map.addLayer(rtlhCluster);
                    console.log("Layer rtlhCluster ditambahkan");
                }

                isRtlhLoaded = true;
                console.log("Data RTLH berhasil dimuat");
            }
        } catch (error) {
            console.error("Error loading RTLH data:", error);
        }
    }
}

// Event listeners
map.on("zoomstart", function () {
    if (activePopup) {
        map.closePopup(activePopup);
        activePopup = null;
    }
});

// Event handler untuk memuat data
map.on("overlayadd", function (e) {
    if (e.name === "Kecamatan " || e.name === "Nama Kecamatan") {
        loadKecamatanData();
    } else if (e.name === "Kel / Desa" || e.name === "Nama Desa") {
        loadDesaData();
    } else if (e.name === "Deliniasi Kumuh") {
        loadKumuhData();
    } else if (e.name === "RTRW ") {
        loadPolaruangData();
    } else if (e.name === "Bedah Rumah") {
        map.whenReady(function () {
            loadHouseData();
        });
    } else if (e.name === "RTLH ") {
        map.whenReady(function () {
            loadRtlhData();
        });
    }
});

map.on("overlayremove", function (e) {
    // Handle Kecamatan layer
    if (e.name === "Kecamatan ") {
        if (map.hasLayer(kecamatan)) {
            map.removeLayer(kecamatan);
            console.log("Layer kecamatan dinonaktifkan");
        }
    }

    // Handle Nama Kecamatan layer
    if (e.name === "Nama Kecamatan") {
        if (map.hasLayer(tooltipKecamatan)) {
            map.removeLayer(tooltipKecamatan);
            console.log("Layer nama kecamatan dinonaktifkan");
        }
    }

    // Handle Desa layer
    if (e.name === "Kel / Desa") {
        if (map.hasLayer(desa)) {
            map.removeLayer(desa);
            console.log("Layer desa dinonaktifkan");
        }
    }

    // Handle Nama Desa layer
    if (e.name === "Nama Desa") {
        if (map.hasLayer(tooltipDesa)) {
            map.removeLayer(tooltipDesa);
            console.log("Layer nama desa dinonaktifkan");
        }
    }

    // Handle Kumuh layer
    if (e.name === "Deliniasi Kumuh") {
        if (map.hasLayer(kumuh)) {
            map.removeLayer(kumuh);
            console.log("Layer kumuh dinonaktifkan");
        }
    }

    // Handle RTRW layer
    if (e.name === "RTRW ") {
        map.removeLayer(polaruang);
        isLayerVisible = false;
        console.log("Polaruang dinonaktifkan");
    }

    // Handle Rumah layer
    if (e.name === "Bedah Rumah" && map.hasLayer(houseCluster)) {
        map.removeLayer(houseCluster);
    }
    if (e.name === "RTLH " && map.hasLayer(rtlhCluster)) {
        map.removeLayer(rtlhCluster);
    }
});

// Event handler untuk memuat data
let groupedOverlays = {
    TEMATIK: {
        "Kecamatan ": kecamatan || {},
        "Nama Kecamatan": tooltipKecamatan || {},
        "Kel / Desa": desa || {},
        "Nama Desa": tooltipDesa || {},
        "Deliniasi Kumuh": kumuh || {},
        "RTRW ": polaruang || {},
        "Bedah Rumah": houseCluster || {},
        "RTLH ": rtlhCluster || {},
    },
};

let baseLayers = null;

// Buat custom layer control
let layerControl = L.control.groupedLayers(baseLayers, groupedOverlays, {
    collapsed: document.body.clientWidth <= 1367,
});

layerControl.addTo(map);

let currentMarker;

let onClicked = function (e) {
    if (currentMarker) {
        map.removeLayer(currentMarker);
    }
    currentMarker = L.marker(e.latlng, {
        icon: L.icon({
            iconUrl: "/img/logobalangan-nav.webp",
            iconAnchor: [12, 28],
            popuplanAnchor: [0, -25],
        }),
    })
        .addTo(map)
        .bindPopup(
            "Koordinat titik ini di " +
                "<br>Lattitude : " +
                e.latlng.lat.toString() +
                "<br>" +
                "Longitude : " +
                e.latlng.lng.toString()
        );
};

map.on("contextmenu", onClicked);
