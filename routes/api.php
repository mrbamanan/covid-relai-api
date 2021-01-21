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
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\PostManagementController;
use App\Http\Controllers\API\SearchController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::post('login', [PassportAuthController::class, 'login']);
Route::post('register', [PassportAuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::prefix('admin')->group(function (){
        Route::get('/user', [PassportAuthController::class, 'user']);

        Route::get('/posts', [PostManagementController::class, 'index']);
        Route::get('/posts/actives', [PostManagementController::class, 'actives']);
        Route::get('/posts/inactives', [PostManagementController::class, 'inActives']);
        Route::post('/posts', [PostManagementController::class, 'store']);
        Route::put('/posts/{id}', [PostManagementController::class, 'update']);
        Route::get('/posts/{id}', [PostManagementController::class, 'show']);
        Route::delete('/posts/{id}', [PostManagementController::class, 'destroy']);
        Route::put('/posts/{id}/status-update', [PostManagementController::class, 'stateUpdate']);
        Route::post('/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::get('/notifications', [NotificationController::class, 'index']);
    });
});

Route::get('/posts', [PostController::class, 'index']);
Route::post('/posts', [PostController::class, 'store']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::post('/posts/search', [SearchController::class, 'search']);
