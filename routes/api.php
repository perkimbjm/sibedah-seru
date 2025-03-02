<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RtlhController;
use App\Http\Controllers\Api\HouseController;
use App\Http\Controllers\Api\VillageController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\TwoFactorAuthenticationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/token', function () {
    return response()->json(['token' => config('services.house')]);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function() {
    // Houses routes
    Route::prefix('houses')->group(function() {
        Route::get('/', [HouseController::class, 'index']);
        Route::get('/{slug}', [HouseController::class, 'show']);
        Route::post('/', [HouseController::class, 'store']);
        Route::put('/{slug}', [HouseController::class, 'update']);
        Route::delete('/{slug}', [HouseController::class, 'destroy']);
    });

    Route::post('/two-factor-authentication', [TwoFactorAuthenticationController::class, 'enable']);
    Route::post('/two-factor-authentication/confirm', [TwoFactorAuthenticationController::class, 'confirm']);
    Route::delete('/two-factor-authentication', [TwoFactorAuthenticationController::class, 'disable']);
    Route::post('/two-factor-authentication/recovery-codes', [TwoFactorAuthenticationController::class, 'regenerateRecoveryCodes']);
    Route::get('/two-factor-authentication/recovery-codes', [TwoFactorAuthenticationController::class, 'getRecoveryCodes']);

});


// Public House routes
Route::prefix('bedah')->group(function() {
    Route::get('/general', [HouseController::class, 'general'])->name('api.bedah.general');
    Route::get('/nearby', [HouseController::class, 'getHousesByRadius'])->name('api.bedah.nearby');
    Route::get('/houses', [HouseController::class, 'getHouses'])->name('api.bedah.houses'); // Untuk <768px
    Route::get('/houses/in-bounds', [HouseController::class, 'getHousesInBounds'])->name('api.bedah.houses.inbounds'); // Untuk >=768px
    Route::get('/houses/{id}', [HouseController::class, 'getHouse'])->name('api.bedah.house'); // Untuk detail rumah
});

// Public District routes
Route::prefix('kecamatan')->group(function() {
    Route::get('/', [DistrictController::class, 'index']);
    Route::get('/geojson', [DistrictController::class, 'geojson']);
    Route::get('/{slug}', [DistrictController::class, 'show']);
    Route::get('/find-by-location', [DistrictController::class, 'findByLocation']);
});

// Public Village routes
Route::prefix('desa')->group(function() {
    Route::get('/', [VillageController::class, 'index']);
    Route::get('/geojson', [VillageController::class, 'geojson']);
    Route::get('/{slug}', [VillageController::class, 'show']);
    Route::get('/find-by-location', [VillageController::class, 'findByLocation']);
});

// RTLH routes
Route::prefix('rtlh')->group(function() {
    Route::get('/', [RtlhController::class, 'index'])->name('api.rtlh');
    Route::get('/houses', [RtlhController::class, 'getRtlh'])->name('api.rtlh.houses');
    Route::get('/districts', [RtlhController::class, 'getDistricts']);
    Route::get('/villages/{district_id}', [RtlhController::class, 'getVillagesByDistrict']);
    Route::get('/villages', [RtlhController::class, 'getVillages']);
    Route::get('/{identifier}', [RtlhController::class, 'searchRtlh'])->name('api.searchRtlh');
    Route::post('/', [RtlhController::class, 'store']);
    Route::put('/{slug}', [RtlhController::class, 'update']);
    Route::delete('/{slug}', [RtlhController::class, 'destroy']);
    Route::get('/status', [RtlhController::class, 'getStatus']);
    Route::get('/find-by-location', [RtlhController::class, 'findByLocation']);
    Route::get('/{slug}', [RtlhController::class, 'show']);
});
