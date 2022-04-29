<?php

use App\Http\Controllers\Api\AppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::post('/register', [AppController::class, 'register'])->middleware('client');

Route::middleware('auth:api')->group(function () {

    Route::get('/bookings', [AppController::class, 'bookings']);
    Route::get('/service', [AppController::class, 'services']);
    Route::post('/bookings/store', [AppController::class, 'store']);
});
