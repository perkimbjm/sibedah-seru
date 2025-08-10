@extends('layouts.main')

@php
    $menuName = str_replace('.', ' ', Route::currentRouteName());
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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
    const totalAduanCount = {{ $totalAduanCount }};
    const aduanDitanggapiCount = {{ $aduanDitanggapiCount }};
    const aduanSelesaiCount = {{ $aduanSelesaiCount }};
    const housesByDistrict = {!! json_encode($housesByDistrict) !!};
    const topVillages = {!! json_encode($topVillages) !!};
    const statusCounts = {!! json_encode($statusCounts) !!};

    // Chart untuk Data Rumah
    const houseCanvas = document.getElementById('houseChart');
    if (houseCanvas) {
        const houseCtx = houseCanvas.getContext('2d');
        const houseChart = new Chart(houseCtx, {
        type: 'bar',
        data: {
            labels: ['Jumlah RTLH', 'Rumah Dibedah'],
            datasets: [{
                label: 'Jumlah',
                data: [rTlhCount, renovatedHouseCount],
                backgroundColor: [
                    'rgba(220, 53, 69, 0.2)',   // danger - red
                    'rgba(0, 123, 255, 0.2)'    // primary - blue
                ],
                borderColor: [
                    'rgba(220, 53, 69, 1)',     // danger - red
                    'rgba(0, 123, 255, 1)'      // primary - blue
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Data Rumah SIBEDAH SERU',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });
    }

    // Chart untuk Data Aduan - hanya jika element ada (conditional)
    const complaintCanvas = document.getElementById('complaintChart');
    if (complaintCanvas) {
        const complaintCtx = complaintCanvas.getContext('2d');
        const complaintChart = new Chart(complaintCtx, {
        type: 'bar',
        data: {
            labels: ['Total Aduan', 'Aduan Ditanggapi', 'Aduan Selesai'],
            datasets: [{
                label: 'Jumlah',
                data: [totalAduanCount, aduanDitanggapiCount, aduanSelesaiCount],
                backgroundColor: [
                    'rgba(23, 162, 184, 0.2)',  // info - cyan
                    'rgba(40, 167, 69, 0.2)',   // success - green
                    'rgba(255, 193, 7, 0.2)'    // warning - yellow
                ],
                borderColor: [
                    'rgba(23, 162, 184, 1)',    // info - cyan
                    'rgba(40, 167, 69, 1)',     // success - green
                    'rgba(255, 193, 7, 1)'      // warning - yellow
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Data Pengaduan Masyarakat',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });
    }

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
        const districtContext = districtCtx.getContext('2d');
        const districtGradient = districtContext.createLinearGradient(0, 0, 0, 400);
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
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
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
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });

        const villageContext = villageCtx.getContext('2d');
        const villageGradient = villageContext.createLinearGradient(0, 0, 0, 400);
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
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: false
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
                        duration: 1500,
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
                        '#9966FF',
                        '#FF9F40'
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
<div class="mt-4 container-fluid">
    <!-- Header -->
    <div class="mb-4 row">
        <div class="col-12">
            <div class="text-center">
                <h1 class="display-4 font-weight-bold text-dark">Statistik SIBEDAH SERU</h1>
                <p class="lead text-muted">Dashboard Sistem Bedah Rumah Kabupaten Balangan</p>
            </div>
        </div>
    </div>

    <!-- Statistik Utama Rumah -->
    <div class="mb-4 row">
        <div class="mb-4 col-lg-6 col-md-6">
            <div class="border-0 shadow-sm card h-100">
                <div class="p-4 text-center card-body">
                    <div class="mb-3">
                        <i class="fas fa-home text-danger" style="font-size: 2.5rem;"></i>
                    </div>
                    <h1 class="mb-2 font-weight-bold text-danger">{{ $rtlhsCount }}</h1>
                    <h5 class="mb-1 text-muted">Jumlah RTLH</h5>
                    <small class="text-muted">Rumah Tidak Layak Huni</small>
                </div>
            </div>
        </div>
        <div class="mb-4 col-lg-6 col-md-6">
            <div class="border-0 shadow-sm card h-100">
                <div class="p-4 text-center card-body">
                    <div class="mb-3">
                        <i class="fas fa-hammer text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h1 class="mb-2 font-weight-bold text-primary">{{ $renovatedHousesCount }}</h1>
                    <h5 class="mb-1 text-muted">Rumah yang Dibedah</h5>
                    <small class="text-muted">Rumah yang Sudah Diperbaiki</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Aduan -->
    @if($canViewAduanStats)
    <div class="mb-4 row">
        <div class="col-12">
            <div class="border-0 shadow-sm card">
                <div class="card-header bg-gradient-light">
                    <h4 class="mb-0 font-weight-bold text-dark">
                        <i class="mr-2 fas fa-comments text-info"></i>
                        Statistik Pengaduan Masyarakat
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="mb-3 col-lg-4 col-md-6">
                            <div class="text-white border-0 card bg-info">
                                <div class="text-center card-body">
                                    <div class="mb-2">
                                        <i class="fas fa-inbox" style="font-size: 2rem;"></i>
                                    </div>
                                    <h3 class="mb-1 font-weight-bold">{{ $totalAduanCount }}</h3>
                                    <p class="mb-0">Total Aduan Masuk</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-4 col-md-6">
                            <div class="text-white border-0 card bg-success">
                                <div class="text-center card-body">
                                    <div class="mb-2">
                                        <i class="fas fa-reply" style="font-size: 2rem;"></i>
                                    </div>
                                    <h3 class="mb-1 font-weight-bold">{{ $aduanDitanggapiCount }}</h3>
                                    <p class="mb-0">Aduan Ditanggapi</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-4 col-md-12">
                            <div class="text-white border-0 card bg-warning">
                                <div class="text-center card-body">
                                    <div class="mb-2">
                                        <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                                    </div>
                                    <h3 class="mb-1 font-weight-bold">{{ $aduanSelesaiCount }}</h3>
                                    <p class="mb-0">Aduan Selesai</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Grafik Statistik -->
    <div class="mb-4 row">
        <div class="mb-4 col-lg-6">
            <div class="border-0 shadow-sm card h-100">
                <div class="card-header bg-gradient-light">
                    <h4 class="mb-0 font-weight-bold text-dark">
                        <i class="mr-2 fas fa-home text-danger"></i>
                        Statistik Data Rumah
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="houseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @if($canViewAduanStats)
        <div class="mb-4 col-lg-6">
            <div class="border-0 shadow-sm card h-100">
                <div class="card-header bg-gradient-light">
                    <h4 class="mb-0 font-weight-bold text-dark">
                        <i class="mr-2 fas fa-comments text-info"></i>
                        Statistik Data Aduan
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="complaintChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Charts dan Map -->
    <div class="mb-4 d-md-flex justify-content-between">
        <div class="m-3 w-md-50 w-100">
            <h4 class="font-weight-bold">Jumlah Rumah Berdasarkan Aspek Keseluruhan</h4>
            <canvas id="statusChart" class="h-60 w-100"></canvas>
        </div>
        <div class="m-3 w-md-50 w-100">
            <h4 class="mb-6 font-weight-bold">Peta Jumlah RTLH per Kecamatan</h4>
            <div id="map"></div>
        </div>
    </div>

    <!-- Charts per Kecamatan dan Desa -->
    <div class="mb-4 row">
        <div class="mb-4 col-12">
            <div class="border-0 shadow-sm card">
                <div class="card-header bg-gradient-light">
                    <h5 class="mb-0 font-weight-bold text-dark">Jumlah Rumah yang Dibedah per Kecamatan</h5>
                </div>
                <div class="card-body">
                    <canvas id="districtChart"></canvas>
                </div>
            </div>
        </div>
        <div class="mb-4 col-12">
            <div class="border-0 shadow-sm card">
                <div class="card-header bg-gradient-light">
                    <h5 class="mb-0 font-weight-bold text-dark">10 Desa Terbanyak yang Rumahnya Dibedah</h5>
                </div>
                <div class="card-body">
                    <canvas id="villageChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .bg-gradient-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
    }

    .card {
        border-radius: 10px;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

    .row:nth-child(2) .card:nth-child(1) { animation-delay: 0.1s; }
    .row:nth-child(2) .card:nth-child(2) { animation-delay: 0.2s; }
    .row:nth-child(3) .card { animation-delay: 0.3s; }
    .row:nth-child(4) .card { animation-delay: 0.4s; }
    .row:nth-child(5) .card { animation-delay: 0.5s; }
    .row:nth-child(6) .card { animation-delay: 0.6s; }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .leaflet-container {
        background: transparent;
        border-color: blueviolet;
    }

    .chart-container {
        position: relative;
        height: 450px;
        width: 100%;
    }



    #districtChart,
    #villageChart,
    #houseChart,
    #complaintChart {
        height: 350px !important;
        width: 100% !important;
    }

    .display-3 {
        font-size: 3.5rem;
        font-weight: 700;
    }

    .card-header h4,
    .card-header h5 {
        font-weight: 600;
        color: #495057;
    }

    .card-header i {
        font-size: 1.2rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-header {
        padding: 1rem 1.5rem;
        border-radius: 10px 10px 0 0;
    }

    .container-fluid {
        padding: 0 2rem;
    }

        /* Responsive adjustments */
    @media (max-width: 768px) {
        .display-3 {
            font-size: 2.5rem;
        }

        .display-4 {
            font-size: 2rem;
        }

        .chart-container {
            height: 400px;
        }

        #houseChart,
        #complaintChart {
            height: 300px !important;
        }

        .leaflet-container {
            height: 300px;
        }

        .container-fluid {
            padding: 0 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .card-header {
            padding: 0.75rem 1rem;
        }
    }

    @media (max-width: 576px) {
        .display-3 {
            font-size: 2rem;
        }

        .display-4 {
            font-size: 1.5rem;
        }

        .container-fluid {
            padding: 0 0.5rem;
        }

        .card-body {
            padding: 0.75rem;
        }

        .card-header {
            padding: 0.5rem 0.75rem;
        }

                .chart-container {
            height: 300px;
        }

        #houseChart,
        #complaintChart {
            height: 250px !important;
        }

        .leaflet-container {
            height: 250px;
        }
    }
</style>
@endsection
