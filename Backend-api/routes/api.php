<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\{AuthController};
use App\Http\Controllers\API\V1\Industry\{IndustryController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Handle CORS options requests globally for any route
Route::options('{all:.*}', function () {
    return response()->json(['status' => 'success']);
})->name('options');

// Group all version 1 routes together
Route::prefix('v1')->group(function () {
    // Unprotected routes
    Route::get('/', function () { return "Staging Environment Working Fine locally and cloudly";});
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Protected routes with JWT authentication
    Route::middleware('jwt.auth')->group(function () {

        Route::get('/get_industries', [IndustryController::class, 'getIndustries']);

    });

});

