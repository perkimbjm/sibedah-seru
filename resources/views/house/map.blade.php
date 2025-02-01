@extends('layouts.main')

@php
    $menuName = 'Peta Sebaran Bedah Rumah';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="map-container">
    <!-- Sidebar -->
    <div id="sidebar" class="active">
        <x-sidebar></x-sidebar>
    </div>

    <div id="sidebar-edit" class="active">
        <x-sidebar-edit></x-sidebar-edit>
    </div>

    <!-- Page Content -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="my-2 ml-auto mr-2">
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <i class="fas fa-bars"></i>
                    <span>Tambah Data</span>
                </button>
                <a href="{{ route('app.houses.index') }}" class="my-2 btn btn-success" style="color: white !important;">Kembali ke Tabel</a>

            </div>
        </nav>

        <div id="mapid"></div>
        <div class="map-hint">
            Klik pada peta untuk memilih lokasi atau geser marker merah untuk menyesuaikan posisi
        </div>

    </div>
</div>
@endsection

@section('styles')
<x-leaflet></x-leaflet>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<link rel="stylesheet" href="{{ asset('css/map-styles.css') }}">

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
    const MAP_CONFIG = {
        center: {
            lat: {{ config('leaflet.map_center_latitude') }},
            lng: {{ config('leaflet.map_center_longitude') }}
        },
        zoom: {{ config('leaflet.zoom_level') }},
        tileLayer: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
    };

    const map = initializeMap();


    function initializeMap() {
        const map = L.map('mapid', { zoomControl: false })
            .setView([MAP_CONFIG.center.lat, MAP_CONFIG.center.lng], MAP_CONFIG.zoom);

        L.tileLayer(MAP_CONFIG.tileLayer, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        return map;
    }


        $("#sidebarCollapse").on("click", () => {
            $("#sidebar").toggleClass("active");
            $("#sidebar-edit").addClass("active");
            map.invalidateSize();
        });

    const markers = L.markerClusterGroup();

    // Konfigurasi popup content
    const createPopupContent = (properties) => {
        const { name, id, address, district, year } = properties;
        return `
            ${name}
            <br>ID: ${id}
            <br>Alamat: ${address}
            <br>Kecamatan: ${district}
            <br>Tahun: ${year}
            <br><button class="mt-2 btn btn-sm bg-danger" onclick="HouseEditor.editHouse(${id})">Edit</button>
        `;
    };

    // Konversi data ke format GeoJSON
    const convertToGeoJSON = (data) => ({
        type: "FeatureCollection",
        features: data.map(item => ({
            type: "Feature",
            geometry: {
                type: "Point",
                coordinates: [item.lng, item.lat]
            },
            properties: {
                id: item.id,
                name: item.name,
                address: item.address,
                district: item.district.name,
                year: item.year
            }
        }))
    });

    // Konfigurasi marker layer
    const createMarkerLayer = (geoJsonData) => {
        return L.geoJSON(geoJsonData, {
            pointToLayer: (geoJsonPoint, latlng) => {
                return L.marker(latlng).bindPopup(layer => createPopupContent(layer.feature.properties));
            }
        });
    };

    // Fungsi utama untuk memuat dan menampilkan data
    const loadMapData = async () => {
        try {
            const response = await fetch('{{ route('api.bedah.general') }}');
            if (!response.ok) {
                throw new Error(`HTTP error ${response.status}`);
            }

            const { data } = await response.json();
            if (!Array.isArray(data)) {
                throw new Error("Data yang diterima bukan array");
            }

            const geoJsonData = convertToGeoJSON(data);
            const markerLayer = createMarkerLayer(geoJsonData);

            markers.addLayer(markerLayer);
            map.addLayer(markers);

        } catch (error) {
            console.error('Error loading map data:', error);
        }
    };


    // Village and District Data Handling
    const LocationDataHandler = {
        loadVillages: () => {
            $.ajax({
                url: '{{ route("app.houses.getDesa") }}',
                method: 'GET',
                success: (data) => {
                    data.forEach(item => {
                        $('#village_id').append(new Option(item.name, item.id));
                    });
                },
                error: () => console.error('Gagal mengambil data desa')
            });
        },

        handleVillageChange: () => {
            const handleDistrictUpdate = (selectedValue, isEdit = false) => {
                const prefix = isEdit ? 'edit_' : '';

                $.ajax({
                    url: `{{ route('app.houses.getKecamatan') }}?village_id=${selectedValue}`,
                    type: 'GET',
                    success: (data) => {
                        if (data.district_name) {
                            $(`#${prefix}district_name`).val(data.district_name);
                            $(`#${prefix}district_id`).val(data.district_id);
                        } else {
                            $(`#${prefix}district_name`).val('');
                            $(`#${prefix}district_id`).val('');
                            alert('Kecamatan tidak ditemukan.');
                        }
                    },
                    error: (xhr, status, error) => console.log('Terjadi kesalahan:', error)
                });
            };

            $("#village_id").change(function() {
                handleDistrictUpdate($(this).val());
            });

            $("#edit_village_id").change(function() {
                handleDistrictUpdate($(this).val(), true);
            });
        }
    };

    // NIK Checker and Form Filler
    const NIKHandler = {
        init: () => {
            $('#nik').on('input', function() {
                const nik = $(this).val();
                if (nik.length === 16) {
                    NIKHandler.checkNIK(nik);
                } else {
                    NIKHandler.resetNIKUI();
                }
            });

            $('#fillDataButton').click(function() {
                NIKHandler.fillFormData($(this).data('houseData'));
            });
        },

        checkNIK: (nik) => {
            $.ajax({
                url: '{{ route("app.houses.checkNIK") }}',
                type: 'GET',
                data: { nik },
                success: (response) => {
                    if (response.exists) {
                        $('#nikAlert, #fillDataButton').show();
                        $('#fillDataButton').data('houseData', response.house);
                    } else {
                        NIKHandler.resetNIKUI();
                    }
                },
                error: (xhr, status, error) => console.log('Error:', error)
            });
        },

        fillFormData: (houseData) => {
            $('#name').val(houseData.name);
            $('#address').val(houseData.address);
            $('#lat').val(houseData.lat);
            $('#lng').val(houseData.lng);
        },

        resetNIKUI: () => {
            $('#nikAlert, #fillDataButton').hide();
        }
    };

    // Map Location Picker
    const LocationPicker = {
        isActive: false,
        marker: null,

        init: () => {
            $('#togglePicker, #edit_togglePicker').on('click', LocationPicker.toggle);
            LocationPicker.initMapEvents();
            LocationPicker.initInputEvents();
        },

        toggle: function() {
            LocationPicker.isActive = !LocationPicker.isActive;
            $('#togglePicker, #edit_togglePicker')
                .toggleClass('btn-info btn-success')
                .html(LocationPicker.isActive ?
                    '<i class="fas fa-map-marker-alt"></i> Klik untuk Menonaktifkan' :
                    '<i class="fas fa-map-marker-alt"></i> Pilih Lokasi di Peta');
            $('.map-hint').toggle(LocationPicker.isActive);
        },

        deactivate: () => {
            LocationPicker.isActive = false;
            $('#togglePicker, #edit_togglePicker')
                .removeClass('btn-success')
                .addClass('btn-info')
                .html('<i class="fas fa-map-marker-alt"></i> Pilih Lokasi di Peta');
            $('.map-hint').hide();

            if (LocationPicker.marker) {
                map.removeLayer(LocationPicker.marker);
                LocationPicker.marker = null;
            }
        },

        createMarker: (latlng) => {
            return L.marker(latlng, {
                draggable: true,
                icon: L.icon({
                    iconUrl: '{{ asset("img/marker-icon-red.png") }}',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: '{{ asset("img/marker-shadow.png") }}',
                    shadowSize: [41, 41]
                })
            });
        },

        updateCoordinateInputs: (latlng) => {
            // Cek sidebar mana yang sedang aktif
            const isEditSidebarActive = !$('#sidebar-edit').hasClass('active');
            const prefix = isEditSidebarActive ? 'edit_' : '';

            document.getElementById(prefix + 'lat').value = latlng.lat.toFixed(6);
            document.getElementById(prefix + 'lng').value = latlng.lng.toFixed(6);
        },

        initMapEvents: () => {
            map.on('click', function(e) {
                if (!LocationPicker.isActive) return;

                if (LocationPicker.marker) {
                    map.removeLayer(LocationPicker.marker);
                }

                LocationPicker.marker = LocationPicker.createMarker(e.latlng)
                    .addTo(map)
                    .on('dragend', event => {
                        LocationPicker.updateCoordinateInputs(event.target.getLatLng());
                    });

                LocationPicker.updateCoordinateInputs(e.latlng);
            });
        },

        initInputEvents: () => {
            // Update untuk kedua set input (normal dan edit)
            ['lat', 'lng', 'edit_lat', 'edit_lng'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', LocationPicker.updateMarkerFromInputs);
            });
        },

        updateMarkerFromInputs: () => {
            if (!LocationPicker.isActive) return;

            // Cek sidebar mana yang sedang aktif
            const isEditSidebarActive = !$('#sidebar-edit').hasClass('active');
            const prefix = isEditSidebarActive ? 'edit_' : '';

            const lat = parseFloat(document.getElementById(prefix + 'lat').value);
            const lng = parseFloat(document.getElementById(prefix + 'lng').value);

            if (!isNaN(lat) && !isNaN(lng)) {
                const newLatLng = L.latLng(lat, lng);

                if (LocationPicker.marker) {
                    LocationPicker.marker.setLatLng(newLatLng);
                } else {
                    LocationPicker.marker = LocationPicker.createMarker(newLatLng)
                        .addTo(map)
                        .on('dragend', event => {
                            LocationPicker.updateCoordinateInputs(event.target.getLatLng());
                        });
                }

                map.setView(newLatLng, map.getZoom());
            }
        }
    };

    // Form Submission Handler
    const FormHandler = {
        init: () => {
            document.getElementById('houseData').addEventListener('submit', FormHandler.handleSubmit);
            document.getElementById('houseEditData').addEventListener('submit', FormHandler.handleEditSubmit);
        },

        handleSubmit: function(event) {
            event.preventDefault();
            FormHandler.submitForm(this, 'Data berhasil disimpan!');
        },

        handleEditSubmit: function(event) {
            event.preventDefault();
            FormHandler.submitForm(this, 'Data berhasil diperbarui!');
        },

        submitForm: (form, successMessage) => {
            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(JSON.stringify(err));
                    });
                }
                return response.json();
            })
            .then(data => {
                UIHelper.showToast(successMessage, 'success');
                setTimeout(() => window.location.reload(), 2000);
            })
            .catch(error => {
                console.error('Error:', error);
                UIHelper.showToast('Gagal menyimpan data: ' + error.message, 'error');
            });
        }
    };

    // UI Helper Functions
    const UIHelper = {
        showToast: (message, type) => {
            const toast = document.createElement('div');
            toast.className = `custom-toast toast-${type}`;
            toast.textContent = message;

            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 100);

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        },

        resetFormAndCloseSidebar: () => {
            document.getElementById('houseData').reset();
            $("#sidebar").addClass("active");
            if (LocationPicker.isActive) {
                LocationPicker.deactivate();
            }
            map.invalidateSize();
        },

        resetEditFormAndCloseSidebar: () => {
            document.getElementById('houseEditData').reset();
            $("#sidebar-edit").addClass("active");
            if (LocationPicker.isActive) {
                LocationPicker.deactivate();
            }
            map.invalidateSize();
        }
    };

    // House Edit Handler
    const HouseEditor = {
        editHouse: (id) => {

            fetch(`{{ route("app.bedah.getData") }}?id=${id}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    $("#sidebar").addClass("active");
                    HouseEditor.fillEditForm(data);
                    $("#sidebar-edit").removeClass("active");
                    map.invalidateSize();
                })
                .catch(error => {
                    console.error('Error:', error);
                    UIHelper.showToast('Gagal mengambil data: ' + error.message, 'error');
                });
        },

        fillEditForm: (data) => {
            const fields = ['id', 'nik', 'name', 'address', 'village_id', 'year', 'type', 'source', 'lat', 'lng', 'note'];
            fields.forEach(field => {
                $(`#edit_${field}`).val(data[field]);
            });

            $('#edit_district_name').val(data.district.name);
            $('#edit_district_id').val(data.district_id);
            $('#edit_village_id').trigger('change');
        }
    };

    window.HouseEditor = HouseEditor;

    // Initialize everything when document is ready
    $(document).ready(() => {
        loadMapData();
        LocationDataHandler.loadVillages();
        LocationDataHandler.handleVillageChange();
        NIKHandler.init();
        LocationPicker.init();
        FormHandler.init();
        $('#cancelBtn').on('click', UIHelper.resetFormAndCloseSidebar);
        $('#edit_cancelBtn').on('click', UIHelper.resetEditFormAndCloseSidebar);

        // Initialize Select2
        $('#edit_village_id').select2({
            placeholder: "Pilih Kelurahan / Desa",
            allowClear: true
        });

        // Sidebar mobile close handler untuk kedua sidebar
        $("#closeSidebarMobile, #edit_closeSidebarMobile").on("click", function() {
            const isEditSidebar = $(this).attr('id') === 'edit_closeSidebarMobile';
            if (isEditSidebar) {
                $("#sidebar-edit").addClass("active");
            } else {
                $("#sidebar").addClass("active");
            }
            map.invalidateSize();
        });

        // Hide map hint initially
        $('.map-hint').hide();
    });
</script>
@endsection
