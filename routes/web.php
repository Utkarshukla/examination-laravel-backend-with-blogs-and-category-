<?php

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
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});
// routes/web.php
Route::get('/auth/google/redirect', [GoogleLoginController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);



Route::get('/checkout',[PaymentController::class,'checkout']);
Route::get('/sucess',[PaymentController::class,'success']);
Route::get('/cancel',[PaymentController::class,'cancel']);


