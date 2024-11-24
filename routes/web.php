<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Models\Gallery;
use Inertia\Inertia;
use App\Http\Controllers\ProxyController;


Route::get('/', function () {
    return Inertia::render('LandingPage');
})->name('landingpage');

Route::get('/download', function () {
    return Inertia::render('DownloadData');
})->name('download');


Route::get('/home', function () {
    return view('home', ['title' => 'Beranda']);
})->name('home');

Route::get('/gallery', function () {
    return view('gallery', ['title' => 'Galeri Kegiatan Bedah Rumah', 'galleries' => Gallery::all()]);
});

Route::get('/gallery/{gallery:slug}', function (Gallery $gallery) {
    
    // $gallery = Gallery::find($slug);
    return view('gallery-detail', ['title' => 'Galeri Detail', 'gallery' => $gallery]);
});

// Route::get('/map', function () {
//     return view('map', ['title' => 'Peta Digital']);
// })->name('map');

Route::get('/map', function () {
    return Inertia::render('Map/index');
})->name('map');

Route::get('/webgis', function () {
    return Inertia::render('WebGis');
})->name('webgis');

// Route::get('/complaint', function () {
//     return view('complaint', ['title' => 'Pengaduan']);
// });

// Route::get('/contact', function () {
//     return view('contact', ['title' => 'Kontak']);
// });

Route::get('/bedah', function () {
    return Inertia::render('HomeListing');
})->name('bedah');

Route::get('/test', function () {
    return Inertia::render('TestLogin');
})->name('test');

Route::get('/guide', function () {
    return Inertia::render('Guide');
})->name('guide');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('/proxy/wfs', [ProxyController::class, 'proxyGeoserverWFS'])->name('proxyWFS');
Route::get('/proxy/wms', [ProxyController::class, 'proxyGeoserverWMS'])->name('proxyWMS');



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
