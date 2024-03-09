<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FrontendMediaController;

Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login',[LoginController::class,"login"]);

Route::middleware('auth:api')->post('/logout', [LoginController::class, 'logout']);
// Post Routes
Route::get('/posts', [PostController::class, 'showAll'])->name('posts.showAll');
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    //Category Routes
    Route::post('/category',[CategoryController::class, 'store'])->name('postCategories');
});
    
Route::get('/categories',[CategoryController::class, 'index'])->name('getCategories');
Route::post('/frontendmedia',[FrontendMediaController::class,'store']);
Route::get('/frontendmedia',[FrontendMediaController::class,'show']);



