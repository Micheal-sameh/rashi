<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BonusPenaltyController;
use App\Http\Controllers\Api\CompetitionController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuizQuestionController;
use App\Http\Controllers\Api\RewardController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserAnswerController;
use App\Http\Controllers\Api\UserController;
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

Route::get('/swagger', function () {
    // Define which documentation to use, defaulting to 'default' if not set
    $documentation = config('l5-swagger.documentation') ?? 'default';
    $useAbsolutePath = true;

    return view('vendor.l5-swagger.index', compact('documentation', 'useAbsolutePath'));
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::group(['middleware' => 'setlocale'], function () {
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::group(['middleware' => ['auth:sanctum', 'check.competition', 'cache.auth.user']], function () {
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}/show', [UserController::class, 'show']);
        Route::post('/profile-pic', [UserController::class, 'profilePic']);
    });

    Route::group(['prefix' => 'competitions'], function () {
        Route::get('/', [CompetitionController::class, 'index'])->name('api.competitions.index');
    });

    Route::group(['prefix' => 'quizzes'], function () {
        Route::get('/', [QuizController::class, 'index']);
    });

    Route::group(['prefix' => 'questions'], function () {
        Route::get('/', [QuizQuestionController::class, 'index']);
    });

    Route::group(['prefix' => 'user-answers'], function () {
        Route::post('/', [UserAnswerController::class, 'store']);
    });

    Route::group(['prefix' => 'rewards'], function () {
        Route::get('/', [RewardController::class, 'index']);
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/my-orders', [OrderController::class, 'myOrders']);
        Route::post('/', [OrderController::class, 'create']);
    });

    Route::group(['prefix' => 'bonus-penalties'], function () {
        Route::get('/', [BonusPenaltyController::class, 'index']);
        Route::post('/', [BonusPenaltyController::class, 'store']);
        Route::get('/{id}', [BonusPenaltyController::class, 'show']);
    });
});

Route::get('/quizzes/dropdown/{id}', [QuizController::class, 'dropdown']);
Route::get('/versions', [SettingController::class, 'getVersions']);

// });
