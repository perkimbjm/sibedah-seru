<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RtlhController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RtlhMapController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HouseMapController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\HousePhotoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\RenovatedHousePhotoController;



Route::get('/', function() {
    $statisticController = app(StatisticController::class);
    $faqController = app(FaqController::class);
    $linkController = app(LinkController::class);
    
    $statistics = $statisticController->getData();
    $faqs = $faqController->getData();
    $links = $linkController->getData();
     
    return Inertia::render('LandingPage', [
        'statistics' => $statistics,
        'faqs' => $faqs,
        'links' => $links
    ]);
})->name('landingpage');

Route::get('/download', function () {
    $downloadController = app(DownloadController::class);
    $downloads = $downloadController->getData();
    $linkController = app(LinkController::class);
    $links = $linkController->getData();
    return Inertia::render('DownloadData', ['downloads' => $downloads, 'links' => $links] );
})->name('download');

Route::get('/terms', function () {
    return Inertia::render('TermsOfService');
})->name('terms');

Route::get('/policy', function () {
    return Inertia::render('PrivacyPolicy');
})->name('policy');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');


Route::get('/home', function () {
    return view('home', ['title' => 'Beranda']);
})->name('home');


Route::get('/gallery/{gallery:slug}', function (Gallery $gallery) {
    
    // $gallery = Gallery::find($slug);
    return view('gallery-detail', ['title' => 'Galeri Detail', 'gallery' => $gallery]);
});


Route::get('/map', function () {
    return Inertia::render('Map/index');
})->name('map');

Route::get('/webgis', function () {
    return Inertia::render('WebGis');
})->name('webgis');


Route::get('/bedah', function () {
    $linkController = app(LinkController::class);
    $links = $linkController->getData();
    return Inertia::render('HomeListing', ['links' => $links]);
})->name('bedah');

Route::get('/test', function () {
    return Inertia::render('HouseInfo');
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
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin routes dengan prefix 'app'
    Route::prefix('app')->name('app.')->group(function () {
        // Routes yang memerlukan permission `role_access`
        Route::middleware(['can:user_management_access'])->group(function () {
            // User Management
            Route::delete('roles/destroy', [RoleController::class, 'massDestroy'])->name('roles.massDestroy');
            Route::delete('users/destroy', [UserController::class, 'massDestroy'])->name('users.massDestroy');
            Route::delete('permissions/destroy', [PermissionController::class, 'massDestroy'])->name('permissions.massDestroy');
            Route::resources([
                'roles' => RoleController::class,
                'users' => UserController::class,
                'permissions' => PermissionController::class,
                // 'audit-logs' => AuditLogController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']],
            ]);
        });

        // Data Management
        Route::middleware(['can:data_access'])->group(function () {
            Route::get('houses/getKecamatan', [HouseController::class, 'getKecamatan'])->name('houses.getKecamatan');
            Route::get('houses/check-nik', [HouseController::class, 'checkNIK'])->name('houses.checkNIK');
            Route::get('rtlh/getKecamatan', [RtlhController::class, 'getKecamatan'])->name('rtlh.getKecamatan');
            Route::delete('houses/destroy', [HouseController::class, 'massDestroy'])->name('houses.massDestroy');
            Route::delete('rtlh/destroy', [RtlhController::class, 'massDestroy'])->name('rtlh.massDestroy');
            Route::resources([
                'houses' => HouseController::class,
                'rtlh' => RtlhController::class,
                'documents' => DocumentController::class,
                'reviews' => ReviewController::class,
            ]);
            Route::prefix('houses/{house}/gallery')->group(function () {
                Route::get('/', [RenovatedHousePhotoController::class, 'index'])->name('gallery.index');
                Route::get('/create', [RenovatedHousePhotoController::class, 'create'])->name('gallery.create');
                Route::post('/', [RenovatedHousePhotoController::class, 'store'])->name('gallery.store');
                Route::get('/{photo}/edit', [RenovatedHousePhotoController::class, 'edit'])->name('gallery.edit');
                Route::put('/{photo}', [RenovatedHousePhotoController::class, 'update'])->name('gallery.update');
                Route::delete('/{photo}', [RenovatedHousePhotoController::class, 'massDestroy'])->name('gallery.massDestroy');
            });
            Route::prefix('rtlh/{rtlh}/rutilahu')->group(function () {
                Route::get('/', [HousePhotoController::class, 'index'])->name('rutilahu.index');
                Route::get('/create', [HousePhotoController::class, 'create'])->name('rutilahu.create');
                Route::post('/', [HousePhotoController::class, 'store'])->name('rutilahu.store');
                Route::get('/{photo}/edit', [HousePhotoController::class, 'edit'])->name('rutilahu.edit');
                Route::put('/{photo}', [HousePhotoController::class, 'update'])->name('rutilahu.update');
                Route::delete('/{photo}', [HousePhotoController::class, 'massDestroy'])->name('rutilahu.massDestroy');
            });
            
            Route::get('bedah/peta', [HouseMapController::class, 'index'])->name('bedah.peta');
            Route::get('rutilahu/peta', [RtlhMapController::class, 'index'])->name('rutilahu.peta');
        });

        // Content Management
        Route::middleware(['can:content_management_access'])->group(function () {
            Route::delete('faqs/destroy', [FaqController::class, 'massDestroy'])->name('faqs.massDestroy');
            Route::delete('downloads/destroy', [DownloadController::class, 'massDestroy'])->name('downloads.massDestroy');
            Route::delete('links/destroy', [LinkController::class, 'massDestroy'])->name('links.massDestroy');
            Route::resources([
                'faqs' => FaqController::class,
                'downloads' => DownloadController::class,
                'links' => LinkController::class,
                'contacts' => ContactController::class,
            ]);
        });
    });
});