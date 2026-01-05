<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BonusPenaltyController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\SettingController;
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

        // Password Reset Routes
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
    });

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::get('/{id}/show', [UserController::class, 'show'])->name('users.show');
            Route::get('/leaderboard', [UserController::class, 'leaderboard'])->name('users.leaderboard');
            Route::get('/leaderboard/export', [UserController::class, 'exportLeaderboard'])->name('users.leaderboard.export');
            Route::put('/{id}/update-groups', [UserController::class, 'updateGroups'])->name('users.updateGroups');
        });

        Route::prefix('competitions')->group(function () {
            Route::get('/', [CompetitionController::class, 'index'])->name('competitions.index');
            Route::get('/create', [CompetitionController::class, 'create'])->name('competitions.create');
            Route::get('/{id}/edit', [CompetitionController::class, 'edit'])->name('competitions.edit');
            Route::get('/{id}/user-answers', [CompetitionController::class, 'userAnswers'])->name('competitions.userAnswers');
            Route::get('/{id}/leaderboard/export', [CompetitionController::class, 'exportLeaderboard'])->name('competitions.leaderboard.export');
            // Route::post('/change-status', [CompetitionController::class, 'changeStatus'])->name('competitions.changeStatus');
            Route::post('/', [CompetitionController::class, 'store'])->name('competitions.store');
            Route::put('/{id}/cancel', [CompetitionController::class, 'cancel'])->name('competitions.cancel');
            Route::put('/{id}/change-status', [CompetitionController::class, 'changeStatus'])->name('competitions.changeStatus');
            Route::put('/{id}/set-active', [CompetitionController::class, 'setActive'])->name('competitions.setActive');
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
            Route::post('/delete-all-tokens', [SettingController::class, 'deleteAllTokens'])->name('settings.deleteAllTokens');
        });

        Route::prefix('groups')->group(function () {
            Route::get('/', [GroupController::class, 'index'])->name('groups.index');
            Route::get('/create', [GroupController::class, 'create'])->name('groups.create');
            Route::get('/{id}/edit', [GroupController::class, 'edit'])->name('groups.edit');
            Route::get('/{id}/users-edit', [GroupController::class, 'usersedit'])->name('groups.usersedit');
            Route::put('/{id}', [GroupController::class, 'update'])->name('groups.update');
            Route::put('/{id}/update-users', [GroupController::class, 'updateUsers'])->name('groups.updateUsers');
            Route::post('/', [GroupController::class, 'store'])->name('groups.store');
        });
        Route::prefix('rewards')->group(function () {
            Route::get('/', [RewardController::class, 'index'])->name('rewards.index');
            Route::get('/create', [RewardController::class, 'create'])->name('rewards.create');
            Route::get('/edit/{id}', [RewardController::class, 'edit'])->name('rewards.edit');
            Route::put('/{id}/add-quantity', [RewardController::class, 'addQuantity'])->name('rewards.addQuantity');
            Route::put('/{id}/cancel', [RewardController::class, 'cancel'])->name('rewards.cancel');
            Route::post('/', [RewardController::class, 'store'])->name('rewards.store');
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('orders.index');
            Route::put('/received/{id}', [OrderController::class, 'received'])->name('orders.received');
            Route::put('/cancel/{id}', [OrderController::class, 'cancel'])->name('orders.cancel');
            // Route::get('/create', [RewardController::class, 'create'])->name('rewards.create');
            // Route::get('/edit/{id}', [RewardController::class, 'edit'])->name('rewards.edit');
            // Route::put('/{id}/add-quantity', [RewardController::class, 'addQuantity'])->name('rewards.addQuantity');
            // Route::post('/', [RewardController::class, 'store'])->name('rewards.store');
        });

        Route::prefix('bonus-penalties')->group(function () {
            Route::get('/', [BonusPenaltyController::class, 'index'])->name('bonus-penalties.index');
            Route::get('/create', [BonusPenaltyController::class, 'create'])->name('bonus-penalties.create');
            Route::post('/', [BonusPenaltyController::class, 'store'])->name('bonus-penalties.store');
            Route::get('/{id}', [BonusPenaltyController::class, 'show'])->name('bonus-penalties.show');
        });

        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
            Route::get('/create', [NotificationController::class, 'create'])->name('notifications.create');
            Route::post('/', [NotificationController::class, 'store'])->name('notifications.store');
        });

        Route::prefix('about_us')->group(function () {
            Route::get('/show', [SettingController::class, 'aboutUs'])->name('about_us.show');
            Route::get('/edit', [SettingController::class, 'editAboutUs'])->name('about_us.edit');
            Route::put('/update', [SettingController::class, 'updateAboutUs'])->name('about_us.update');
        });

        Route::prefix('terms')->group(function () {
            Route::get('/show', [SettingController::class, 'aboutUs'])->name('terms.show');
            Route::get('/edit', [SettingController::class, 'editAboutUs'])->name('terms.edit');
            Route::put('/update', [SettingController::class, 'updateAboutUs'])->name('terms.update');
        });

    });
});
