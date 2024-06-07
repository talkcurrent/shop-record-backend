<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('/user', 'user');
        Route::post('/get_categories', 'get_categories');
        Route::post('/get_sub_categories', 'get_sub_categories');
        Route::post('/create_store', 'create_store');
        Route::post('/get_stores', 'get_stores');
    });

Route::post('/save_categories', [GuestController::class, 'saveCategories']);

Route::post('/register', [GuestController::class, 'register']);
Route::post('/login', [GuestController::class, 'login'])->name('login');
Route::get('/get_countries', [GuestController::class, 'get_countries']);
Route::post('/get_states', [GuestController::class, 'get_states']);
Route::post('/get_localGovts', [GuestController::class, 'get_localGovts']);
