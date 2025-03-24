<?php

use App\Http\Controllers\CryptocurrencyController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HistoricalDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

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


Route::get('/cryptocurrencies', [CryptocurrencyController::class, 'index']);
Route::get('/getcrypto', [CryptocurrencyController::class, 'getCryptos']);
Route::post('/favorites', [FavoriteController::class, 'store']);
Route::get('/favoritesIndex', [FavoriteController::class, 'index']);
Route::get('/historical-data/{cryptocurrency}', [HistoricalDataController::class, 'show']);

