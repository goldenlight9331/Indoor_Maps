<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Auth\RegisterController;


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
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Api Routes

// Route::post('register', [ApiController::class, 'register']);

Route::get('company', [ApiController::class, 'Company']);
Route::get('/videos/{filename}', [ApiController::class, 'getVideo']);
Route::post('History', [ApiController::class, 'History']);

Route::post('login', [ApiController::class, 'login']);

Route::group(["middleware" => ["auth"]], function () {
    Route::get("profile", [ApiController::class, 'profile']);
    Route::post("logout", [ApiController::class, 'logout']);
});

Route::get('/log-interval', [ApiController::class, 'logInterval']);
Route::post('/store-logs', [ApiController::class, 'storeLogs']);