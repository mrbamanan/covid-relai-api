<?php


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

use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PassportAuthController;
use App\Http\Controllers\API\PostGuestController;
use App\Http\Controllers\API\PostAuthController;
use App\Http\Controllers\API\SearchController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

//Client 4 Secret: MsSvBSHwADb4o2EuMtTcnVt9sBM81kn9NLSdQx6V


Route::middleware('client')->group(function () {
    Route::post('login', [PassportAuthController::class, 'login']);
    Route::post('register', [PassportAuthController::class, 'register']);

    Route::apiResource('/', PostGuestController::class)->only(['index', 'show', 'store']);
    Route::post('search', [SearchController::class, 'search']);

    Route::middleware('auth:api')->group(function () {
        Route::apiResource('posts', PostAuthController::class);
        Route::get('posts/inactives', [PostAuthController::class, 'inActives']);
        Route::get('posts/actives', [PostAuthController::class, 'actives']);
        Route::post('posts/{id}/update-state', [PostAuthController::class, 'stateUpdate']);
        Route::post('posts/{id}/comment', [PostAuthController::class, 'storeComment']);

        Route::get('notifications', [NotificationController::class, 'index']);
        //Route::get('notifications/{id}', [NotificationController::class, 'show']);
        //Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);

    });

});
