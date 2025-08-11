<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\FavoritController;
use App\Http\Controllers\KomentarController;
use App\Http\Controllers\KomentarController as ControllersKomentarController;

// use App\Http\Controllers\ResepController as ControllersResepController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Route
Route::middleware(['auth', 'json'])->prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth');
    Route::delete('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::prefix('setting')->group(function () {
    Route::get('', [SettingController::class, 'index']);
});

Route::middleware(['auth', 'verified', 'json'])->group(function () {
    Route::prefix('setting')->middleware('can:setting')->group(function () {
        Route::post('', [SettingController::class, 'update']);
    });

    Route::prefix('master')->group(function () {
        Route::middleware('can:master-user')->group(function () {
            Route::get('users', [UserController::class, 'get']);
            Route::post('users', [UserController::class, 'index']);
            Route::post('users/store', [UserController::class, 'store']);
            Route::apiResource('users', UserController::class)
                ->except(['index', 'store'])->scoped(['user' => 'uuid']);
        });

        Route::middleware('can:master-role')->group(function () {
            Route::get('roles', [RoleController::class, 'get'])->withoutMiddleware('can:master-role');
            Route::post('roles', [RoleController::class, 'index']);
            Route::post('roles/store', [RoleController::class, 'store']);
            Route::apiResource('roles', RoleController::class)
                ->except(['index', 'store']);
        });
    });
    
    
    
    // Protected routes (gunakan sanctum atau auth middleware jika perlu)
    Route::middleware('can:resep')->group(function () {
        // Route::post('/resep', [ResepController::class, 'index']);
        // Route::get('/resep/{id}', [ResepController::class, 'show']);
        Route::post('/resep/store', [ResepController::class, 'store']);
        // Route::post('resep', [ResepController::class, 'index']);
        // Route::post('/resep', [ResepController::class, 'index'])->withoutMiddleware('can:resep');
        Route::post('/resep/{id}/favorit', [FavoritController::class, 'toggle']);
        Route::post('/resep/{id}/komentar', [KomentarController::class, 'store']);
        Route::post('/resep/{id}/rating', [RatingController::class, 'store']);
    });
    Route::put('/kategori/{id}', [KategoriController::class, 'tambahKategori'])->withoutMiddleware('can:resep');
    // Route::post('/kategori/{id}', [KategoriController::class, 'store']);
    Route::post('/resep', [ResepController::class, 'index']);
    // Route::get('/kategori', [KategoriController::class, 'show']);
    Route::post('/kategori', [KategoriController::class, 'store']);
    // Route::get('/resep/{resep}', [KategoriController::class, 'show']);
    Route::resource('kategori', KategoriController::class);
    // Route::get('/resep', [KategoriController::class, 'show'])->withoutMiddleware('can:resep');
    Route::get('/resep/{resep}', [ResepController::class, 'show']);


    

});
