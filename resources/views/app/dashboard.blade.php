@extends('layouts.main')

@php
    $menuName = str_replace('.', ' ', Route::currentRouteName());
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    #map {
        height: 68vh;
        width: 100%;
    }
</style>
@endsection


@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Data untuk chart
    const rTlhCount = {{ $rtlhsCount }};
    const renovatedHouseCount = {{ $renovatedHousesCount }};
    const housesByDistrict = {!! json_encode($housesByDistrict) !!};
    const topVillages = {!! json_encode($topVillages) !!};
    const statusCounts = {!! json_encode($statusCounts) !!};

    // Logika untuk membuat chart menggunakan Chart.js
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jumlah RTLH', 'Jumlah Rumah yang Dibedah'],
            datasets: [{
                label: 'Jumlah',
                data: [rTlhCount, renovatedHouseCount],
                backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Pastikan data dalam format array
    const districtLabels = @json($housesByDistrict->keys()->toArray());
    const districtData = @json($housesByDistrict->values()->toArray());

    const villageLabels = @json($villageIds->map(function($villageId) use ($villages) {
        $village = $villages->where('id', $villageId)->first();
        return $village ? $village->name : 'Unknown';
    })->toArray());
    const villageData = @json($topVillages->values()->toArray());

    const statusLabels = @json(array_keys($statusCounts->toArray()));
    const statusData = @json(array_values($statusCounts->toArray()));


    // Pastikan canvas elements ada
    const districtCtx = document.getElementById('districtChart');
    const villageCtx = document.getElementById('villageChart');
    const statusCtx = document.getElementById('statusChart');

    if (districtCtx && villageCtx) {
        const districtGradient = districtCtx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        districtGradient.addColorStop(0, 'rgba(54, 162, 235, 0.8)');
        districtGradient.addColorStop(1, 'rgba(54, 162, 235, 0.2)');

        new Chart(districtCtx, {
            type: 'bar',
            data: {
                labels: districtLabels,
                datasets: [{
                    label: 'Jumlah Rumah yang Dibedah per Kecamatan',
                    data: districtData,
                    backgroundColor: districtGradient,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Statistik per Kecamatan'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        const villageGradient = villageCtx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        villageGradient.addColorStop(0, 'rgba(255, 99, 132, 0.8)');
        villageGradient.addColorStop(1, 'rgba(255, 99, 132, 0.2)');


        // Pastikan villageLabels dan villageData memiliki panjang yang sama
        if (Array.isArray(villageLabels) && Array.isArray(villageData) && villageLabels.length === villageData.length) {
            new Chart(villageCtx, {
                type: 'bar',
                data: {
                    labels: villageLabels,
                    datasets: [{
                        label: 'Jumlah Rumah yang Dibedah per Desa',
                        data: villageData,
                        backgroundColor: villageGradient,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Top 10 Desa'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        } else {
            console.error('Data atau label tidak valid untuk chart desa.');
        }
    }

    if (statusCtx) {
        const statusChart = new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Jumlah RTLH menurut kategori',
                    data: statusData,
                    backgroundColor: [
                        '#36A2EB',
                        '#FF6384',
                        '#FFCE56',
                        '#4BC0C0',

                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'left',
                    },
                    title: {
                        display: true,
                        text: 'Jumlah RTLH'
                    }
                }
            }
        });
    }

    // Inisialisasi peta
    const map = L.map('map').setView([-2.3371967578715145, 115.60049646030521], 10); // Sesuaikan koordinat dan zoom level
    map.removeControl(map.zoomControl);


    // Ambil data GeoJSON kecamatan
    fetch('/api/kecamatan/geojson')
        .then(response => response.json())
        .then(kecamatanData => {
            // Data jumlah RTLH berdasarkan district_id
            const rtlhCounts = @json($rtlhCounts->toArray()); // Ambil data dari controller
            // Tambahkan jumlah RTLH ke data GeoJSON kecamatan
            kecamatanData.features.forEach(feature => {
                const districtId = feature.properties.id;
                feature.properties.rtlhCount = rtlhCounts[districtId] || 0; // Simpan jumlah RTLH
            });

            // Buat choropleth map
            L.geoJson(kecamatanData, {
                style: function(feature) {
                    return {
                        fillColor: '#2171b5', // Warna biru
                        weight: 2,
                        opacity: 1,
                        color: 'white',
                        dashArray: '3',
                        fillOpacity: 0.7
                    };
                },
                onEachFeature: function(feature, layer) {
                    layer.bindTooltip(
                        `<strong>${feature.properties.name}</strong><br>Jumlah RTLH: ${feature.properties.rtlhCount}`,
                        {sticky: true}
                    );
                }
            }).addTo(map);
        })
        .catch(error => console.error('Error loading kecamatan data:', error));

    // Fungsi untuk menentukan warna berdasarkan jumlah RTLH
    function getColor(rtlhCount) {
        return rtlhCount > 400 ? '#084594' :
               rtlhCount > 300 ? '#2171b5' :
               rtlhCount > 200 ? '#4292c6' :
               rtlhCount > 100 ? '#6baed6' :
               rtlhCount > 0  ? '#9ecae1' :
                                '#c6dbef';
    }
</script>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h2 class="font-weight-bold">Statistik SIBEDAH SERU</h2>
                    <div class="scoreboard">
                        <div class="score-item">
                            <h1 class="text-danger">{{ $rtlhsCount }}</h1>
                            <p class="text-muted">Jumlah RTLH</p>
                        </div>
                        <div class="score-item">
                            <h1 class="text-primary">{{ $renovatedHousesCount }}</h1>
                            <p class="text-muted">Jumlah Rumah yang Dibedah</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4"></div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-4 ml-4 d-md-flex justify-content-between">
        <div class="w-md-50 w-100 m-3">
            <h4 class="font-weight-bold">Jumlah Rumah Berdasarkan Aspek Keseluruhan</h4>
            <canvas id="statusChart" class="w-100 h-60"></canvas>
        </div>
        <div class="w-md-50 w-100 m-3">
            <h4 class="font-weight-bold mb-6">Peta Jumlah RTLH per Kecamatan</h4>
            <div id="map"></div>
        </div>
    </div>
    <div class="mb-4 ml-4">
        <h4 class="font-weight-bold">Jumlah Rumah yang Dibedah per Kecamatan</h4>
        <canvas id="districtChart"></canvas>
    </div>
    <div class="mb-4 ml-1">
        <h4 class="font-weight-bold">10 Desa Terbanyak yang Rumahnya Dibedah</h4>
        <canvas id="villageChart"></canvas>
    </div>
</div>
@endsection

@section('styles')
<style>
    .scoreboard {
        display: flex;
        justify-content: space-around;
        align-items: center;
        margin-top: 20px;
    }
    .score-item {
        text-align: center;
    }

    .leaflet-container {
        background: transparent;
        border-color: blueviolet;
    }
</style>
@endsection
