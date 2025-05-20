<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['setlocale']], function () {
    // Language change routes (optional, if you want to switch languages via URL)
    Route::get('/lang/{lang}', function ($lang) {
        // You can redirect to a page after changing the language
        session(['lang' => $lang]);  // Store the language in session

        return redirect()->back();
    });

    Route::prefix('auth')->group(function () {
        Route::get('/login', [AuthController::class, 'loginPage'])->name('loginPage');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::get('/{id}/show', [UserController::class, 'index'])->name('users.show');
        });

        Route::prefix('competitions')->group(function () {
            Route::get('/', [CompetitionController::class, 'index'])->name('competitions.index');
        });
    });
});
