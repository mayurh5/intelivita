<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaderboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [LeaderboardController::class, 'index'])->name('index');

Route::group(['prefix' => 'leaderboard', 'namespace'=>'leaderboard', 'as'=>'leaderboard.'], function () {
    Route::post('/json', [LeaderboardController::class, 'json'])->name('json');
    Route::post('/recalculate', [LeaderboardController::class, 'recalculate'])->name('recalculate');
});

// Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
// Route::post('/leaderboard/recalculate', [LeaderboardController::class, 'recalculate'])->name('leaderboard.recalculate');

