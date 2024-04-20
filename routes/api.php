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

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FrontendMediaController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\ContactForm;
use App\Http\Controllers\OlympiadController;

Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login',[LoginController::class,"login"]);

Route::middleware('auth:api')->post('/logout', [LoginController::class, 'logout']);
Route::middleware('auth:api')->get('/refresh-token', [LoginController::class, 'refreshToken']);
Route::middleware('auth:api')->get('/verify-email', [LoginController::class, 'verifyEmail']);
Route::get('/verify-email/{email}/{token}', [LoginController::class, 'verifyEmailToken']);





// Post Routes
Route::get('/posts', [PostController::class, 'showAll'])->name('posts.showAll');
Route::get('/olympiads',[OlympiadController::class,'index']);
Route::get('/olympiads/{id}',[OlympiadController::class,'show']);
//Routes for logged in user 
Route::group(['middleware' => ['auth:api']], function () {
    //admin only
    Route::middleware(['checkRole:1'])->group(function () {
    
        Route::get('/a/test', function (){
            return "hi superadmin";
        });
        Route::post('/admin/olympiad/create',[OlympiadController::class,'create' ]);
        Route::put('/admin/olympiad/update/{id}',[OlympiadController::class,'update' ]);
        Route::delete('/admin/olympiad/destroy/{id}',[OlympiadController::class,'destroy' ]);
       
    });
    //incharge only
    Route::middleware(['checkRole:2'])->group(function () {
       
        Route::get('/i/test', function (){
            return "hi incharge";
        });
    });

    Route::middleware(['checkRole:1,2'])->group(function () {
    
        Route::get('/c/test', function (){
            return "hi common";
        });
    });
    //student only
    Route::middleware(['checkRole:5'])->group(function () {
        
        Route::get('s/test', function (){
            return "hi student";
        });
    });

    //for all user
    Route::middleware(['checkRole:1,2,3'])->group(function () {
    
        Route::get('/c/test', function (){
            return "hi common";
        });
        
        Route::get('/olympiads/{id}',[OlympiadController::class,'show']);
    });






    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    //Category Routes
    Route::post('/category',[CategoryController::class, 'store'])->name('postCategories');

    Route::get('/post/{postid}/comment/',[CommentController::class,'show']);
    Route::post('/post/{postid}/comment/',[CommentController::class,'store']);
});
    






Route::get('/categories',[CategoryController::class, 'index'])->name('getCategories');

Route::get('/frontendmedia',[FrontendMediaController::class,'show']);
Route::get('frontendmedia/{page}',[FrontendMediaController::class, 'find']);

Route::post('/contact',[ContactForm::class, 'index']);

Route::get('/auth/google/redirect', [GoogleLoginController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);