<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RtlhController;
use App\Http\Controllers\Api\HouseController;
use App\Http\Controllers\Api\VillageController;
use App\Http\Controllers\Api\DistrictController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/rutilahu', [RutilahuController::class, 'index']);

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

});


// Public House routes
Route::prefix('houses')->group(function() {
    Route::get('/geojson', [HouseController::class, 'geojson']);
    Route::get('/years', [HouseController::class, 'getYears']);
    Route::get('/types', [HouseController::class, 'getTypes']);
    Route::get('/find-by-location', [HouseController::class, 'findByLocation']);
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
    Route::get('/', [RtlhController::class, 'index']);
    Route::get('/{slug}', [RtlhController::class, 'show']);
    Route::post('/', [RtlhController::class, 'store']);
    Route::put('/{slug}', [RtlhController::class, 'update']);
    Route::delete('/{slug}', [RtlhController::class, 'destroy']);
    Route::get('/map', [RtlhController::class, 'map']);
    Route::get('/geojson', [RtlhController::class, 'geojson']);
    Route::get('/status', [RtlhController::class, 'getStatus']);
    Route::get('/find-by-location', [RtlhController::class, 'findByLocation']);
});

Route::get('/routes', function() {
    return Route::getRoutes();
});