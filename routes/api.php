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
use App\Http\Controllers\API\SearchController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::post('login', [PassportAuthController::class, 'login']);
//Route::post('register', [PassportAuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('posts', PostController::class)->except('show', 'update', 'delete');

    Route::get('/posts/{post:slug}', [PostController::class, 'show']);
    Route::put('/posts/{post:slug}', [PostController::class, 'update']);
    Route::delete('/posts/{post:slug}', [PostController::class, 'destroy']);

    Route::get('posts/inactives', [PostController::class, 'inActives']);
    Route::get('posts/actives', [PostController::class, 'actives']);

    Route::get('/user', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        return response()->json($user);
    });

    Route::get('notifications', [NotificationController::class, 'index']);

    //Route::get('notifications/{id}', [NotificationController::class, 'show']);
    //Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);

});

Route::get('posts', [PostController::class, 'index']);
Route::post('posts', [PostController::class, 'store']);

Route::post('search', [SearchController::class, 'search']);

