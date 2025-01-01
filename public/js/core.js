window.DesaSearch = [];

const CACHE_DURATION = 30 * 60 * 1000; // 30 minutes
const CHUNK_SIZE = 500;

const mapCenter = [-2.33668, 115.46028];
const mapZoom = 18;

const dataCache = {
    house: { data: null, timestamp: null },
    rtlh: { data: null, timestamp: null }
};

const debouncedInvalidateSize = _.debounce(() => {
    if (map) {
        map.invalidateSize();
    }
}, 250);

// Tambahkan event listener
window.addEventListener('resize', debouncedInvalidateSize);

const worker = new Worker('/js/marker-worker.js');
let markerQueue = [];
let isProcessingQueue = false;


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
}

if (L && L.Control && L.Control.geocoder) {
    L.Control.geocoder({
        position: "topleft",
        collapsed: true,
    }).addTo(map);
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

/*Scale Map*/
if (L && L.control && L.control.scale) {
    L.control.scale({ imperial: false }).addTo(map);
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
    { title: "Voyager", layer: cartoVoyager, icon: baseMapIcons.cartoVoyager },
    { title: "RBI", layer: rbi, icon: baseMapIcons.rbi },
    { title: "Google Maps", layer: googleMaps, icon: baseMapIcons.googleMaps },
    { title: "Google Imagery", layer: googleSatellite, icon: baseMapIcons.googleSatellite },
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

let ctlMeasure = L.control.polylineMeasure({ position: "topleft" }).addTo(map);

const showModal = (title, content) => {
    document.getElementById("feature-title").innerText = title;
    document.getElementById("feature-info").innerHTML = content;
    const modal = document.getElementById("featureModal");
    if (modal) {
        modal.classList.remove("hidden");
    }
};

const zoomToFeature = (e) => {
    map.fitBounds(e.target.getBounds());
};
function loadGeoJsonData(url, layer, tooltipLayer, tooltipProperty, filters = {}) {
    const params = new URLSearchParams(filters).toString();
    const fetchUrl = params ? `${url}?${params}` : url;

    fetch(fetchUrl)
        .then((response) => response.json())
        .then((data) => {
            layer.clearLayers();
            if (tooltipLayer) {
                tooltipLayer.clearLayers();
            }

            layer.addData(data);

            if (tooltipLayer && tooltipProperty) {
                data.features.forEach((feature) => {
                    const geoJsonLayer = L.geoJSON(feature);
                    const updateTooltipPosition = () => {
                        const center = geoJsonLayer.getBounds().getCenter();
                        tooltip.setLatLng(center);
                    };

                    let tooltip = L.tooltip({
                        permanent: true,
                        direction: "center",
                        className: "no-background",
                    })
                        .setContent(feature.properties[tooltipProperty])
                        .setLatLng(geoJsonLayer.getBounds().getCenter());

                    tooltipLayer.addLayer(tooltip);

                    map.on("zoomend", updateTooltipPosition);
                    map.on("moveend", updateTooltipPosition);
                });
            }
        })
        .catch((error) => {
            console.error("Error loading GeoJSON data:", error);
        });
}

window.loadGeoJsonData = loadGeoJsonData;

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
        loadGeoJsonData(
            "/api/kecamatan/geojson",
            kecamatan,
            tooltipKecamatan,
            "name"
        );
        isKecamatanLoaded = true;
    }
}

// Buat fungsi untuk memuat data desa
window.desa = L.geoJson(null, {
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

window.tooltipDesa = L.layerGroup();
let isDesaLoaded = false;

// Fungsi untuk memuat data desa
function loadDesaData() {
    let params = {};

    if (window.selectedDistrict) {
        params.district_id = window.selectedDistrict;
    }
    if (window.selectedVillage) {
        params.village_id = window.selectedVillage;
    }

    loadGeoJsonData(
        "/api/desa/geojson",
        window.desa,
        window.tooltipDesa,
        "name",
        params
    );
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
    time: new Date().getTime(),
    cacheControl: "max-age=3600",
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
        if (map && polaruang) {
            map.addLayer(polaruang);
        }
        isPolaruangLoaded = true;
        isLayerVisible = true;
    } else if (!isLayerVisible) {
        if (map && polaruang) {
            map.addLayer(polaruang);
        }
        isLayerVisible = true;
    }
}

const createClusterGroup = () => {
    return L.markerClusterGroup({
        maxClusterRadius: (zoom) => {
            if (zoom <= 8) return 120;
            if (zoom <= 12) return 80;
            if (zoom <= 15) return 40;
            return 20;
        },
        chunkedLoading: true,
        chunkInterval: 200,
        chunkDelay: 50,
        spiderfyOnMaxZoom: false,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true,
        disableClusteringAtZoom: 14,
        maxZoom: 18,
        animate: false,
        removeOutsideVisibleBounds: true,
        spiderfyDistanceMultiplier: 1.5,
        zoomAnimation: true,
        animateAddingMarkers: false
    });
};
const isValidLatLng = (lat, lng) => {
    return lat !== null && lng !== null &&
           !isNaN(lat) && !isNaN(lng) &&
           lat >= -90 && lat <= 90 &&
           lng >= -180 && lng <= 180;
};

function isCacheValid(type) {
    const cache = dataCache[type];
    return cache.data && cache.timestamp && 
           (Date.now() - cache.timestamp) < CACHE_DURATION;
}

async function processMarkerQueue(targetLayer, targetCluster) {
    if (isProcessingQueue || markerQueue.length === 0) return;
    
    isProcessingQueue = true;
    
    while (markerQueue.length > 0) {
        const chunk = markerQueue.splice(0, CHUNK_SIZE);
        const geoJsonChunk = {
            type: "FeatureCollection",
            features: chunk
        };

        targetLayer.addData(geoJsonChunk);
        targetCluster.addLayer(targetLayer);

        // Give browser time to breathe
        await new Promise(resolve => setTimeout(resolve, 50));
    }
    
    isProcessingQueue = false;
}

window.house = L.geoJson(null, {
    pointToLayer: function (feature, latlng) {
        if (!isValidLatLng(latlng.lat, latlng.lng)) {
            console.warn("Invalid coordinates detected:", latlng);
            return null;
        }

        const marker = L.marker(latlng, {
            icon: L.icon({
                iconUrl: "/img/home-blue.png",
                iconSize: [24, 28],
                iconAnchor: [12, 28],
                popupAnchor: [0, -25],
            }),
        });

        marker.options.clickable = true;
        marker.options.riseOnHover = true;
        marker.options.bubblingMouseEvents = false;

        return marker;
    },
    onEachFeature: function (feature, layer) {
        if (feature.properties) {
            const properties = feature.properties;
            const content = `<div class='p-2 shadow rounded-md'>
                <table class='table-auto w-full border-collapse border border-gray-400'>
                    ${Object.entries({
                        ID: properties.id,
                        Nama: properties.name,
                        Alamat: properties.address,
                        Kecamatan: properties.district?.name,
                        Tahun: properties.year,
                        Tipe: properties.type,
                        Sumber: properties.source,
                        Catatan: properties.note,
                    }).map(([key, value]) => `<tr><th class='text-left text-gray-700 bg-gray-200 px-1'>${key}</th><td class='text-gray-600'>${value || "-"}</td></tr>`).join('')}
                </table>
            </div>`;
            const popup = L.popup({
                closeButton: true,
                autoPan: false,
                maxWidth: 300,
                closeOnClick: true,
                autoClose: true
            }).setContent(content);

            layer.bindPopup(popup);
        }
    },
    filter: function (feature) {
        const coords = feature.geometry.coordinates;
        return isValidLatLng(coords[1], coords[0]);
    },
});

window.house = house;
window.initHouseLayer = function(map, handleHouseClick) {
    return window.house;
};

window.fetchData = async function(url) {
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    } catch (error) {
        console.error("Error fetching data:", error);
        throw error;
    }
};

const rtlh = L.geoJson(null, {
    pointToLayer: function (feature, latlng) {
        if (!isValidLatLng(latlng.lat, latlng.lng)) {
            console.warn("Invalid coordinates detected:", latlng);
            return null;
        }

        const marker = L.marker(latlng, {
            icon: L.icon({
                iconUrl: "/img/home-red.png",
                iconSize: [24, 28],
                iconAnchor: [12, 28],
                popupAnchor: [0, -25],
            }),
        });

        marker.options.clickable = true;
        marker.options.riseOnHover = true;
        marker.options.bubblingMouseEvents = false;

        return marker;
    },
    onEachFeature: function (feature, layer) {
        if (feature.properties) {
            const properties = feature.properties;
            const content = `<div class='p-2 shadow rounded-md'>
            <table class='table-auto w-full border-collapse border border-gray-400'>
                ${Object.entries({
                    ID: properties.id,
                    Nama: properties.name,
                    Alamat: properties.address,
                    "Jumlah Penghuni": properties.people,
                    "Luas rumah (m2)": properties.area,
                    Pondasi: properties.pondasi,
                    Atap: properties.atap,
                    Dinding: properties.dinding,
                    Lantai: properties.lantai,
                    "Kelayakan Bangunan": properties.status_safety,
                    "Kelayakan (Keseluruhan)": properties.status,
                    Catatan: properties.note,
                }).map(([key, value]) => `<tr><th class='text-left text-gray-700 bg-gray-200 px-1'>${key}</th><td class='text-gray-700'>${value || "-"}</td></tr>`).join('')}
            </table>
        </div>`;
            const popup = L.popup({
                closeButton: true,
                autoPan: false,
                maxWidth: 400,
                closeOnClick: true,
                autoClose: true
            }).setContent(content);
    
            layer.bindPopup(popup);
        }
    },
    filter: function (feature) {
        const coords = feature.geometry.coordinates;
        return isValidLatLng(coords[1], coords[0]);
    },
});

window.houseCluster = createClusterGroup();
window.houseCluster = houseCluster;
const rtlhCluster = createClusterGroup();

let isHouseLoaded = false;
let isRtlhLoaded = false;
let activePopup = null;

async function loadHouseData() {
    if (!isHouseLoaded) {
        try {
            let data;
            
            if (isCacheValid('house')) {
                data = dataCache.house.data;
                console.log('Using cached house data');
            } else {
                const response = await fetch("/api/bedah/general");
                data = await response.json();
                
                dataCache.house = {
                    data: data,
                    timestamp: Date.now()
                };
            }

            // Process data using Web Worker
            worker.postMessage({
                action: 'processMarkers',
                data: data.data
            });

            worker.addEventListener('message', async (e) => {
                if (e.data.action === 'markersProcessed') {
                    markerQueue = e.data.data;
                    
                    house.clearLayers();
                    houseCluster.clearLayers();
                    
                    await processMarkerQueue(house, houseCluster);
                    
                    if (!map.hasLayer(houseCluster)) {
                        map.addLayer(houseCluster);
                    }
                }
            });

            isHouseLoaded = true;
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

            const CHUNK_SIZE = 1000;
            const validFeatures = data.data
            .filter(item => isValidLatLng(item.lat, item.lng))
            .map(item => ({
                type: "Feature",
                geometry: {
                    type: "Point",
                    coordinates: [item.lng, item.lat]
                },
                properties: {
                    id: item.id,
                    name: item.name,
                    address: item.address,
                    area: item.area,
                    people: item.people,
                    pondasi: item.pondasi,
                    atap: item.atap,
                    dinding: item.dinding,
                    lantai: item.lantai,
                    status_safety: item.status_safety,
                    status: item.status,
                    note: item.note,
                }
            }));

            for (let i = 0; i < validFeatures.length; i += CHUNK_SIZE) {
                const chunk = validFeatures.slice(i, i + CHUNK_SIZE);
                await new Promise(resolve => setTimeout(resolve, 0));
            }


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
                }

                isRtlhLoaded = true;
            }
        } catch (error) {
            console.error("Error loading RTLH data:", error);
        }
    }
}


// Event listeners
map.on("zoomstart", () => {
    if (activePopup) {
        map.closePopup(activePopup);
        activePopup = null;
    }
    [houseCluster, rtlhCluster].forEach(cluster => {
        if (cluster && cluster.disableClustering) {
            cluster.disableClustering();
        }
    });
});

map.on('zoomend', () => {
    [houseCluster, rtlhCluster].forEach(cluster => {
        if (cluster && cluster.enableClustering) {
            setTimeout(() => cluster.enableClustering(), 100);
        }
    });
});

map.on('error', function(e) {
    console.error('Map error:', e);
    if (e.error && e.error.message === '_latLngToNewLayerPoint') {
        map.flyTo(map.getCenter(), map.getZoom(), {
            animate: true,
            duration: 1.5 // durasi transisi dalam detik
        });
    }
});

window.addEventListener("filterRtlh", async (event) => {
    const filters = event.detail;
    try {
        const params = new URLSearchParams();

        Object.entries(filters).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach((item) => params.append(`${key}[]`, item));
            } else {
                params.append(key, value);
            }
        });

        const response = await axios.get("/api/rtlh/houses", { params });
        const data = response.data;

        if (data.success) {
            updateRtlhLayer(data.data);
        } else {
            console.error("Error filtering RTLH data:", data.message);
        }
    } catch (error) {
        console.error("Error fetching RTLH data:", error);
    }
});

function updateRtlhLayer(data) {
    rtlh.clearLayers();
    rtlhCluster.clearLayers();

    const validFeatures = data
        .filter((item) => isValidLatLng(item.lat, item.lng))
        .map((item) => ({
            type: "Feature",
            geometry: {
                type: "Point",
                coordinates: [item.lng, item.lat],
            },
            properties: item,
        }));

    if (validFeatures.length > 0) {
        const geoJsonData = {
            type: "FeatureCollection",
            features: validFeatures,
        };
        rtlh.addData(geoJsonData);
        rtlhCluster.addLayer(rtlh);
        if (!map.hasLayer(rtlhCluster)) {
            map.addLayer(rtlhCluster);
        } else {
            map.removeLayer(rtlhCluster);
            map.addLayer(rtlhCluster);
        }
    } else {
        if (map.hasLayer(rtlhCluster)) {
            map.removeLayer(rtlhCluster);
        }
    }
}

// DrawItems control script
var drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

map.on("draw:created", function (e) {
    drawnItems.addLayer(e.layer);
});

map.on(L.Draw.Event.CREATED, function (e) {
    drawnItems.addLayer(e.layer);
    let type = e.layerType,
        layer = e.layer;
    if (type === "marker") {
        let cord = layer.getLatLng().toString();
        layer.bindPopup(cord).openPopup();
    }
    map.addLayer(layer);
});

drawnItems.on("click", function (e) {
    return;
});

// Konfigurasi Draw Control dan tambahkan ke peta
var drawControl = new L.Control.Draw({
    draw: {
        polyline: true,
        polygon: true,
        rectangle: true,
        circle: true,
        marker: true,
        circlemarker: true,
        shapefile: {
            shapeOptions: {
                color: "blue",
                weight: 3,
                opacity: 1,
                fillOpacity: 0.1,
            },
        },
        geojson: true,
    },
    edit: {
        featureGroup: drawnItems,
    },
});
map.addControl(drawControl);

// Sembunyikan toolbar saat inisialisasi
document.querySelector(".leaflet-draw-toolbar").style.display = "none";

// Sembunyikan semua elemen draw control saat inisialisasi
document
    .querySelectorAll(".leaflet-draw-toolbar, .leaflet-draw-actions")
    .forEach(function (element) {
        element.style.display = "none";
    });

if (L && L.easyButton) {
    L.easyButton({
        states: [
            {
                stateName: "draw",
                icon: "fa fa-wrench fa-lg",
                title: "toggle draw toolbar",
                onClick: function (btn, map) {
                    toggleDrawToolbar();
                },
            },
        ],
    }).addTo(map);
}

// Fungsi untuk menampilkan atau menyembunyikan tombol-tombol leaflet draw
function toggleDrawToolbar() {
    document
        .querySelectorAll(".leaflet-draw-toolbar, .leaflet-draw-actions")
        .forEach(function (element) {
            if (element.style.display === "none") {
                element.style.display = "block";
            } else {
                element.style.display = "none";
            }
        });
}

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
    } else if (e.name === "Hasil Drawing") {
        if (!map.hasLayer(drawnItems)) {
            map.addLayer(drawnItems);
        }
    }
});

map.on("overlayremove", function (e) {
    if (e.name === "Kecamatan ") {
        if (map.hasLayer(kecamatan)) {
            map.removeLayer(kecamatan);
        }
    }

    if (e.name === "Nama Kecamatan") {
        if (map.hasLayer(tooltipKecamatan)) {
            map.removeLayer(tooltipKecamatan);
        }
    }

    if (e.name === "Kel / Desa") {
        if (map.hasLayer(desa)) {
            map.removeLayer(desa);
        }
    }

    if (e.name === "Nama Desa") {
        if (map.hasLayer(tooltipDesa)) {
            map.removeLayer(tooltipDesa);
        }
    }

    if (e.name === "Deliniasi Kumuh") {
        if (map.hasLayer(kumuh)) {
            map.removeLayer(kumuh);
        }
    }

    if (e.name === "RTRW ") {
        map.removeLayer(polaruang);
        isLayerVisible = false;
    }

    if (e.name === "Bedah Rumah" && map.hasLayer(houseCluster)) {
        map.removeLayer(houseCluster);
        houseCluster.clearLayers();
    }
    if (e.name === "RTLH " && map.hasLayer(rtlhCluster)) {
        map.removeLayer(rtlhCluster);
        rtlhCluster.clearLayers();
    }
    if (e.name === "Hasil Draw") {
        if (map.hasLayer(drawnItems)) {
            map.removeLayer(drawnItems);
        }
    }

    if (map) {
        map.invalidateSize();
    }
});

function addLayerToControl(layer, name) {
    if (layer && name && layerControl) {
        if (layer._leaflet_id) {
            groupedOverlays.IMPORT[name] = layer;

            layerControl.remove();

            layerControl = L.control.groupedLayers(
                baseLayers,
                groupedOverlays,
                {
                    collapsed: document.body.clientWidth <= 1367,
                    exclusiveGroups: [],
                }
            );

            layerControl.addTo(map);
        } else {
            console.error(`Layer "${name}" tidak memiliki _leaflet_id yang valid.`);
        }
    } else {
        console.error(`Gagal menambahkan layer "${name}" ke layer control: Parameter tidak valid.`);
    }
}

function popUp(geo) {
    map.fitBounds(geo.getBounds());
    geo.eachLayer(function (layer) {
        let properties = layer.feature.properties;
        let popupContent =
            "<div class='popup-content'><table class='table-auto w-full border-collapse border border-gray-300'>";
        Object.entries(properties).forEach(([key, value]) => {
            popupContent += `<tr><th class='text-left border border-gray-300 p-2'>${key}</th><td class='border border-gray-300 p-2'>${value}</td></tr>`;
        });
        popupContent += "</table></div>";
        layer.bindPopup(popupContent);
    });
}

let geo;

let groupedOverlays = {
    ADMINISTRASI: {
        "Kecamatan ": kecamatan ? kecamatan : null,
        "Nama Kecamatan": tooltipKecamatan ? tooltipKecamatan : null,
        "Kel / Desa": desa ? desa : null,
        "Nama Desa": tooltipDesa ? tooltipDesa : null,
        "RTRW ": polaruang ? polaruang : null,
    },
    TEMATIK: {
        "Deliniasi Kumuh": kumuh ? kumuh : null,
        "Bedah Rumah": houseCluster ? houseCluster : null,
        "RTLH ": rtlhCluster ? rtlhCluster : null,
        "Hasil Draw": drawnItems ? drawnItems : null,
    },
    IMPORT: {},
};

let baseLayers;

function initializeMap() {
    layerControl = L.control.groupedLayers(baseLayers, groupedOverlays, {
        collapsed: document.body.clientWidth <= 1367,
    });
    layerControl.addTo(map);

    if (houseCluster) map.addLayer(houseCluster);
}

initializeMap();

function geoJsonData(file) {
    let reader = new FileReader();
    reader.onload = function () {
        const geoJsonLayer = L.layerGroup();

        const geo = omnivore
            .geojson(reader.result)
            .on("ready", function () {
                this.eachLayer(function (layer) {
                    geoJsonLayer.addLayer(layer);
                });
                popUp(this);
            })
            .addTo(geoJsonLayer);

        geoJsonLayer.addTo(map);

        const fileName = file.name.split(".").slice(0, -1).join(".");

        addLayerToControl(geoJsonLayer, fileName);
    };
    reader.readAsDataURL(file);
}

function gpxData(file) {
    let reader = new FileReader();
    reader.onload = function () {
        const gpxLayer = L.layerGroup();
        const geo = omnivore
            .gpx(reader.result)
            .on("ready", function () {
                popUp(geo);
            })
            .addTo(gpxLayer);
        gpxLayer.addTo(map);
        const fileName = file.name.split(".").slice(0, -1).join(".");
        groupedOverlays.IMPORT[fileName] = gpxLayer;

        layerControl.remove();
        layerControl = L.control.groupedLayers(baseLayers, groupedOverlays, {
            collapsed: document.body.clientWidth <= 1367,
        });
        layerControl.addTo(map);
    };
    reader.readAsDataURL(file);
}

function csvData(file) {
    let reader = new FileReader();
    reader.onload = function () {
        const csvLayer = L.layerGroup();
        const geo = omnivore.csv.parse(reader.result).addTo(csvLayer);
        popUp(geo);
        csvLayer.addTo(map);
        const fileName = file.name.split(".").slice(0, -1).join(".");
        groupedOverlays.IMPORT[fileName] = csvLayer;

        layerControl.remove();
        layerControl = L.control.groupedLayers(baseLayers, groupedOverlays, {
            collapsed: document.body.clientWidth <= 1367,
        });
        layerControl.addTo(map);
    };
    reader.readAsText(file);
}

function kmlData(file) {
    let reader = new FileReader();
    reader.onload = function () {
        const kmlLayer = L.layerGroup();
        const geo = omnivore.kml.parse(reader.result).addTo(kmlLayer);
        popUp(geo);
        kmlLayer.addTo(map);
        const fileName = file.name.split(".").slice(0, -1).join(".");
        groupedOverlays.IMPORT[fileName] = kmlLayer;

        layerControl.remove();
        layerControl = L.control.groupedLayers(baseLayers, groupedOverlays, {
            collapsed: document.body.clientWidth <= 1367,
        });
        layerControl.addTo(map);
    };
    reader.readAsText(file);
}

function wktData(file) {
    let reader = new FileReader();
    reader.onload = function () {
        const wktLayer = L.layerGroup();
        const geo = omnivore.wkt.parse(reader.result).addTo(wktLayer);
        popUp(geo);
        wktLayer.addTo(map);
        const fileName = file.name.split(".").slice(0, -1).join(".");
        groupedOverlays.IMPORT[fileName] = wktLayer;

        layerControl.remove();
        layerControl = L.control.groupedLayers(baseLayers, groupedOverlays, {
            collapsed: document.body.clientWidth <= 1367,
        });
        layerControl.addTo(map);
    };
    reader.readAsText(file);
}

function shpData(file) {
    const reader = new FileReader();
    reader.onload = function (e) {
        const buffer = e.target.result;
        shp(buffer)
            .then(function (geojson) {
                const shpLayer = L.layerGroup();
                const layer = L.geoJson(geojson, {
                    onEachFeature: function (feature, layer) {
                        if (feature.properties) {
                            const keys = Object.keys(feature.properties);
                            const values = Object.values(feature.properties);

                            let popupContent =
                                '<div class="max-h-100px max-w-87.5px overflow-auto">' +
                                '<table class="table-auto w-full border-collapse border border-gray-300">';

                            popupContent += "<tr>";
                            keys.forEach((key) => {
                                popupContent += `<th class="text-left border border-gray-300 p-2 overflow-auto">${key}</th>`;
                            });
                            popupContent += "</tr>";

                            popupContent += "<tr>";
                            values.forEach((value) => {
                                popupContent += `<td class="border border-gray-300 p-2 overflow-auto">${value}</td>`;
                            });
                            popupContent += "</tr>";

                            popupContent += "</table></div>";
                            layer.bindPopup(popupContent);
                        }
                    },
                });
                shpLayer.addLayer(layer);
                shpLayer.addTo(map);
                const fileName = file.name.split(".").slice(0, -1).join(".");
                groupedOverlays.IMPORT[fileName] = shpLayer;

                layerControl.remove();
                layerControl = L.control.groupedLayers(
                    baseLayers,
                    groupedOverlays,
                    {
                        collapsed: document.body.clientWidth <= 1367,
                    }
                );
                layerControl.addTo(map);
                map.fitBounds(layer.getBounds());
            })
            .catch(function (error) {
                console.error("Error loading shapefile:", error);
            });
    };
    reader.readAsArrayBuffer(file);
}

// Add Vector Layers
function vectorData() {
    let inputNode = document.createElement("input");
    inputNode.setAttribute("type", "file");
    inputNode.setAttribute("id", "leaflet-draw-shapefile-selector");
    inputNode.setAttribute("accept", ".geojson,.gpx,.csv,.kml,.wkt,.zip");

    inputNode.addEventListener("change", function (e) {
        let files = inputNode.files;
        let file = files[0];
        let parts = file.name.split(".");
        let ext = parts[parts.length - 1];

        if (ext.toLowerCase() == "geojson") {
            geoJsonData(file);
        } else if (ext.toLowerCase() == "gpx") {
            gpxData(file);
        } else if (ext.toLowerCase() == "csv") {
            csvData(file);
        } else if (ext.toLowerCase() == "kml") {
            kmlData(file);
        } else if (ext.toLowerCase() == "wkt") {
            wktData(file);
        } else if (ext.toLowerCase() == "zip") {
            shpData(file);
        }
    });
    inputNode.click();
}

if (L && L.easyButton) {
    L.easyButton({
        states: [
            {
                stateName: "upload",
                icon: "fa fa-upload fa-lg",
                title: "Add Layers (shapefile, geojson, gpx, csv, kml, wkt)",
                onClick: function (btn, map) {
                    vectorData();
                },
            },
        ],
    }).addTo(map);
}

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