<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\SettingController;
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
    return redirect()->route('competitions.index');
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
            //     Route::get('/', [UserController::class, 'index']);
        });

        Route::prefix('competitions')->group(function () {
            Route::get('/', [CompetitionController::class, 'index'])->name('competitions.index');
            Route::get('/create', [CompetitionController::class, 'create'])->name('competitions.create');
            Route::get('/{id}/edit', [CompetitionController::class, 'edit'])->name('competitions.edit');
            Route::post('/change-status', [CompetitionController::class, 'changeStatus'])->name('competitions.changeStatus');
            Route::post('/', [CompetitionController::class, 'store'])->name('competitions.store');
            Route::put('/{id}/cancel', [CompetitionController::class, 'cancel'])->name('competitions.cancel');
            Route::put('/{id}', [CompetitionController::class, 'update'])->name('competitions.update');
        });

        Route::prefix('quizzes')->group(function () {
            Route::get('/', [QuizController::class, 'index'])->name('quizzes.index');
            Route::get('/create', [QuizController::class, 'create'])->name('quizzes.create');
            Route::get('/{id}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
            Route::post('/', [QuizController::class, 'store'])->name('quizzes.store');
            Route::put('/{id}', [QuizController::class, 'update'])->name('quizzes.update');
            Route::get('/dropdown/{id}', [QuizController::class, 'dropdown'])->name('quizzes.dropdown');
            Route::delete('/{id}', [QuizController::class, 'delete'])->name('quizzes.delete');
        });

        Route::prefix('questions')->group(function () {
            Route::get('/', [QuizQuestionController::class, 'index'])->name('questions.index');
            Route::get('/create', [QuizQuestionController::class, 'create'])->name('questions.create');
            Route::get('/{id}/edit', [QuizQuestionController::class, 'edit'])->name('questions.edit');
            Route::post('/', [QuizQuestionController::class, 'store'])->name('questions.store');
            Route::put('/{id}', [QuizQuestionController::class, 'update'])->name('questions.update');
            Route::delete('/{id}', [QuizQuestionController::class, 'delete'])->name('questions.delete');
        });

        Route::prefix('settings')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('settings.index');
            Route::put('/', [SettingController::class, 'update'])->name('settings.update');
        });

        Route::prefix('groups')->group(function () {
            Route::get('/', [GroupController::class, 'index'])->name('groups.index');
            Route::get('/create', [GroupController::class, 'create'])->name('groups.create');
            Route::get('/{id}/edit', [GroupController::class, 'edit'])->name('groups.edit');
            Route::put('/{id}', [GroupController::class, 'update'])->name('groups.update');
            Route::put('/{id}/update-users', [GroupController::class, 'updateUsers'])->name('groups.updateUsers');
            Route::post('/', [GroupController::class, 'store'])->name('groups.store');
        });
    });
});
