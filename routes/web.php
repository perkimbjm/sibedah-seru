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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RtlhMapController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HouseMapController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\HousePhotoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\Auth\ChangePasswordController;
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
    $linkController = app(LinkController::class);
    $links = $linkController->getData();
    return Inertia::render('Guide', ['links' => $links]);
})->name('guide');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('/proxy/wfs', [ProxyController::class, 'proxyGeoserverWFS'])->name('proxyWFS');
Route::get('/proxy/wms', [ProxyController::class, 'proxyGeoserverWMS'])->name('proxyWMS');

Route::get('file-manager/thumbnail/{path}', [FileManagerController::class, 'showThumbnail'])->where('path', '.*')->name('file-manager.thumbnail');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/webgis', function () {
        return view('app.webgis');
    })->name('webgis');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/password', [ChangePasswordController::class, 'edit'])->name('profile.password.edit');
    Route::post('profile/password', [ChangePasswordController::class, 'update'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deleteProfilePhoto'])->name('profile.photo.delete');

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
            Route::get('houses/getDesa', [HouseController::class, 'getDesa'])->name('houses.getDesa');
            Route::get('rtlh/getDesa', [RtlhController::class, 'getDesa'])->name('rtlh.getDesa');
            Route::get('houses/download-template', [HouseController::class, 'downloadTemplate'])->name('houses.download-template');
            Route::get('rtlh/download-template', [RtlhController::class, 'downloadTemplate'])->name('rtlh.download-template');
            Route::post('houses/import', [HouseController::class, 'import'])->name('houses.import');
            Route::get('houses/import', function () {
                return view('house.import');
            })->name('houses.import.form');
            Route::post('rtlh/import', [RtlhController::class, 'import'])->name('rtlh.import');
            Route::get('rtlh/import', function () {
                return view('rtlh.import');
            })->name('rtlh.import.form');
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
                Route::delete('/{photo}', [RenovatedHousePhotoController::class, 'destroy'])->name('gallery.destroy');
            });
            Route::prefix('rtlh/{rtlh}/rutilahu')->group(function () {
                Route::get('/', [HousePhotoController::class, 'index'])->name('rutilahu.index');
                Route::get('/create', [HousePhotoController::class, 'create'])->name('rutilahu.create');
                Route::post('/', [HousePhotoController::class, 'store'])->name('rutilahu.store');
                Route::get('/{photo}/edit', [HousePhotoController::class, 'edit'])->name('rutilahu.edit');
                Route::put('/{photo}', [HousePhotoController::class, 'update'])->name('rutilahu.update');
                Route::delete('/{photo}', [HousePhotoController::class, 'destroy'])->name('rutilahu.destroy');
            });

            Route::get('bedah/peta', [HouseMapController::class, 'index'])->name('bedah.peta');
            Route::post('bedah/store-map', [HouseMapController::class, 'store'])->name('bedah.store');
            Route::get('/bedah/edit', [HouseMapController::class, 'edit'])->name('bedah.edit');
            Route::put('/bedah/update', [HouseMapController::class, 'update'])->name('bedah.update');
            Route::get('/bedah/get-data', [HouseMapController::class, 'getHouseData'])->name('bedah.getData');

            Route::get('rutilahu/peta', [RtlhMapController::class, 'index'])->name('rutilahu.peta');
            Route::post('rumah/store-map', [RtlhMapController::class, 'store'])->name('rumah.store');
            Route::get('/rumah/edit', [RtlhMapController::class, 'edit'])->name('rumah.edit');
            Route::put('/rumah/update', [RtlhMapController::class, 'update'])->name('rumah.update');
            Route::get('/rumah/get-data', [RtlhMapController::class, 'getRtlhData'])->name('rumah.getData');



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

        Route::middleware(['can:file-manager_access'])->group(function () {
            Route::prefix('file-manager')->group(function () {
                Route::get('/', [FileManagerController::class, 'index'])->name('file-manager.index');
                Route::post('/upload', [FileManagerController::class, 'upload'])->name('file-manager.upload');
                Route::post('/create-folder', [FileManagerController::class, 'createFolder'])->name('file-manager.create-folder');
                Route::delete('/delete', [FileManagerController::class, 'delete'])->name('file-manager.delete');
                Route::post('/rename', [FileManagerController::class, 'rename'])->name('file-manager.rename');
                Route::post('/copy', [FileManagerController::class, 'copy'])->name('file-manager.copy');
                Route::post('/move', [FileManagerController::class, 'move'])->name('file-manager.move');
                Route::post('/extract', [FileManagerController::class, 'extract'])->name('file-manager.extract');
                Route::post('/download-file', [FileManagerController::class, 'downloadFile'])->name('file-manager.download-file');
                Route::post('/download-items', [FileManagerController::class, 'downloadItems'])->name('file-manager.download-items');
            });
         });

    });
});
