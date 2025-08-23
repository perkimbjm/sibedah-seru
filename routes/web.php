<?php

use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RtlhController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\AduanController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\ProxyController;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UsulanController;
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
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\SecurityLogController;
use App\Http\Controllers\NotificationController;
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

            // Security Logs
            Route::get('security-logs', [SecurityLogController::class, 'index'])
                ->name('security-logs.index')
                ->middleware('can:security_log_access');

            // Pastikan route statis didefinisikan sebelum route dinamis
            Route::get('security-logs/statistics', [SecurityLogController::class, 'statistics'])
                ->name('security-logs.statistics')
                ->middleware('can:security_log_statistics');

            Route::post('security-logs/clear', [SecurityLogController::class, 'clearOldLogs'])
                ->name('security-logs.clear')
                ->middleware('can:security_log_clear');

            Route::get('security-logs/{securityLog}', [SecurityLogController::class, 'show'])
                ->name('security-logs.show')
                ->whereNumber('securityLog')
                ->middleware('can:security_log_show');
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

// Password Reset Routes
Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return Inertia::render('Auth/Login');
    })->name('login');

    Route::post('login', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Log successful login
            \App\Models\SecurityLog::logEvent('login_success', [
                'email' => $request->input('email'),
                'status' => 'success'
            ]);

            return redirect()->intended(route('dashboard'));
        }

        // Log failed login attempt
        \App\Models\SecurityLog::logEvent('login_failed', [
            'email' => $request->input('email'),
            'status' => 'failed',
            'reason' => 'Invalid credentials'
        ]);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    })->name('login.post');

    // Register routes with rate limiting
    Route::get('register', function () {
        // Log register page access for debugging
        Log::info('Register page GET request', [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'environment' => app()->environment(),
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId()
        ]);

        return Inertia::render('Auth/Register');
    })->name('register');

    Route::post('register', function (Request $request) {
        // Log register POST request for debugging
        Log::info('Register page POST request', [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'environment' => app()->environment(),
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId(),
            'has_email' => $request->has('email'),
            'has_captcha' => $request->has('captcha')
        ]);

        // Use Fortify's built-in registration handling
        return app(\Laravel\Fortify\Http\Controllers\RegisteredUserController::class)->store(
            $request,
            app(\Laravel\Fortify\Contracts\CreatesNewUsers::class)
        );
    })->middleware(['throttle:register'])->name('register.post');

    Route::get('forgot-password', function () {
        return Inertia::render('Auth/ForgotPassword');
    })->name('password.request');

    Route::post('forgot-password', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('reset-password/{token}', function ($token) {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => request()->query('email'),
        ]);
    })->name('password.reset');

    Route::post('reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    })->name('password.update');
});

// Routes untuk pengaduan
Route::prefix('aduan')->name('aduan.')->group(function () {
    // Public routes
    Route::post('/', [AduanController::class, 'store'])->name('store');
    Route::post('/track', [AduanController::class, 'track'])->name('track');
    Route::post('/add-complaint', [AduanController::class, 'addComplaint'])->name('add-complaint');
    Route::post('/complete', [AduanController::class, 'completeComplaint'])->name('complete');
    Route::post('/evaluate', [AduanController::class, 'addEvaluation'])->name('evaluate');
    Route::get('/captcha', [AduanController::class, 'generateCaptcha'])->name('captcha');
    Route::post('/validate-email', [AduanController::class, 'validateEmail'])->name('validate-email')->middleware('throttle:10,1');
    Route::get('/villages/{district_id}', [AduanController::class, 'getVillagesByDistrict'])->name('villages');

    // Auth required routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/my-complaints', [AduanController::class, 'myComplaints'])->name('my-complaints');
        Route::post('/link-previous', [AduanController::class, 'linkPreviousComplaintsApi'])->name('link-previous');
        Route::post('/{aduan}/continue', [AduanController::class, 'continueComplaint'])->name('continue');
        Route::post('/{aduan}/evaluate', [AduanController::class, 'evaluate'])->name('evaluate');

        // Admin routes (untuk sekarang tidak pakai permission dulu)
        Route::get('/', [AduanController::class, 'index'])->name('index');
        Route::get('/create', [AduanController::class, 'create'])->name('create');
        Route::get('/{aduan}', [AduanController::class, 'show'])->name('show');
        Route::get('/{aduan}/edit', [AduanController::class, 'edit'])->name('edit');
        Route::put('/{aduan}', [AduanController::class, 'update'])->name('update');
        Route::delete('/{aduan}', [AduanController::class, 'destroy'])->name('destroy');
    });
});

// Routes untuk usulan masyarakat
Route::prefix('usulan')->name('usulan.')->group(function () {
    // Public routes untuk informasi mekanisme pengusulan
    Route::get('/info', function () {
        return view('usulan.info');
    })->name('info');

    // Auth required routes
    Route::middleware(['auth'])->group(function () {
        // CRUD Usulan
        Route::resource('proposals', UsulanController::class)->parameters([
            'proposals' => 'usulan'
        ]);

        // Verifikasi management (dipindah ke prefix yang berbeda) - HARUS DI ATAS ROUTE VERIFIKASI BIASA
        Route::prefix('verifikasi-management')->name('verifikasi-management.')->middleware('can:verifikasi_access')->group(function () {
            Route::get('/stats', [VerifikasiController::class, 'getVerifikasiStats'])->name('stats');
            Route::get('/by-usulan/{usulan}', [VerifikasiController::class, 'getVerifikasiByUsulan'])->name('by-usulan');
            Route::get('/verifikasi/{verifikasi}/add-to-rtlh', [VerifikasiController::class, 'addToRtlh'])->name('add-to-rtlh');
            Route::resource('verifikasi', VerifikasiController::class)->parameters([
                'verifikasi' => 'verifikasi'
            ]);
        });

        // Verifikasi usulan
        Route::get('/{usulan}/verifikasi', [UsulanController::class, 'verifikasi'])->name('verifikasi')->middleware('can:usulan_verify');
        Route::post('/{usulan}/verifikasi', [VerifikasiController::class, 'store'])->name('verifikasi.store')->middleware('can:usulan_verify');
        Route::get('/{usulan}/verifikasi/edit', [VerifikasiController::class, 'edit'])->name('verifikasi.edit')->middleware('can:usulan_verify');
        Route::put('/{usulan}/verifikasi', [VerifikasiController::class, 'update'])->name('verifikasi.update')->middleware('can:usulan_verify');

        // API routes untuk validasi real-time
        Route::post('/validate-nik', [UsulanController::class, 'validateNIK'])->name('validate-nik');
        Route::post('/validate-kk', [UsulanController::class, 'validateKK'])->name('validate-kk');
        Route::get('/villages-by-district', [UsulanController::class, 'getVillagesByDistrict'])->name('villages-by-district');

        // API routes untuk notifikasi (DIDEFINISIKAN SEBELUM resource agar tidak tertabrak oleh show route)
        Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/notifications/recent', [NotificationController::class, 'getRecentNotifications'])->name('notifications.recent');
        Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');

        // Notifikasi - batasi hanya route yang dibutuhkan agar tidak membuat konflik (hindari show)
        Route::resource('notifications', NotificationController::class)->only(['index', 'destroy']);
    });
});
