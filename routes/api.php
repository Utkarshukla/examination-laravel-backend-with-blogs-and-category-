<?php

use App\Http\Controllers\AdminController;
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
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FrontendMediaController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\BulkParticipateController;
use App\Http\Controllers\ContactForm;
use App\Http\Controllers\OlympiadController;
use App\Http\Controllers\ParticipateController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\UserController;

Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login',[LoginController::class,"login"]);

Route::middleware('auth:api')->post('/logout', [LoginController::class, 'logout']);
Route::middleware('auth:api')->get('/refresh-token', [LoginController::class, 'refreshToken']);
Route::middleware('auth:api')->get('/verify-email', [LoginController::class, 'verifyEmail']);
Route::get('/verify-email/{email}/{token}', [LoginController::class, 'verifyEmailToken']);

Route::get('/school',[SchoolController::class, 'index']);
Route::get('/school/{id}',[SchoolController::class,'show']);
Route::get('/olympiads',[OlympiadController::class,'index']);
Route::get('/olympiads/{id}',[OlympiadController::class,'show']);
Route::get('/schools',[SchoolController::class,'index']);


//Routes for logged in user 
Route::group(['middleware' => ['auth:api']], function () {
    //admin only
    Route::middleware(['checkRole:1'])->group(function () {

        Route::post('/admin/school/create',[SchoolController::class,'create']);
        
        Route::get('/admin/school/{id}',[SchoolController::class,'show']);
        Route::put('/admin/school/update/{id}', [SchoolController::class,'update']);
        Route::delete('/admin/school/delete/{id}',[SchoolController::class,'destroy']);

        Route::post('/admin/olympiad/create',[OlympiadController::class,'create' ]);
        Route::put('/admin/olympiad/update/{id}',[OlympiadController::class,'update' ]);
        Route::delete('/admin/olympiad/destroy/{id}',[OlympiadController::class,'destroy' ]);

        Route::post('/admin/posts', [PostController::class, 'store'])->name('posts.store');
        Route::put('/admin/posts/{id}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/admin/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');

        Route::post('/admin/category',[CategoryController::class, 'store'])->name('postCategories');

        Route::get('/admin/olympiad/{id}/allparticipate/',[AdminController::class,'olypiad_participates']);
        Route::get('/admin/olympiad/{id}/allparticipate/user/{user_id}/',[AdminController::class,'olypiad_participate_single']);

        Route::post('/admin/olympiad/{id}/bulkhallticket/',[AdminController::class, 'hallticket']);
        Route::post('/admin/olympiad/{id}/bulkcertificates/',[AdminController::class, 'certificates']);
        Route::post('/admin/olympiad/{id}/singleticket/',[AdminController::class,'singlehallticket']);

        Route::get('/admin/olympiad/{id}/getuploadmarkscsv', [AdminController::class,'getuploadmarkscsv']);
        Route::post('/admin/olympiad/{id}/postuploadmarkscsv', [AdminController::class,'postuploadmarkscsv']);

        Route::get('/admin/users/',[AdminController::class,'alluser']);
        Route::get('/admin/users/{id}',[AdminController::class,'singleUser']);
        Route::get('/admin/incharges/',[AdminController::class,'allincharge']);
        Route::get('/admin/incharge/{id}',[AdminController::class,'singleIncharge']);
        Route::get('/admin/pendingIncharge/',[AdminController::class,'pendingIncharge']);
        Route::post('/admin/incharge/{id}/approve/',[AdminController::class,'approveIncharge']);
        Route::post('/admin/incharge/{id}/unapprove/',[AdminController::class,'unapproveIncharge']);
        
    });
    //incharge only
    Route::middleware(['checkRole:2'])->group(function () {
        
    });
    //admin, incharge
    Route::middleware(['checkRole:1,2'])->group(function () {
        Route::post('/incharge/olympiad/register/',[BulkParticipateController::class,'create']);    
        Route::get('/incharge/olympiad/{id}/registered',[ParticipateController::class,'show']);
        Route::get('/incharge/olympiad/your-olympiad',[ParticipateController::class,'showAll']);
        Route::delete('/incharge/olympiad/{id}/registered/{participate_id}',[ParticipateController::class,'deleteOne']);

        Route::get('/incharge/olympiad/{id}/checkout',[PaymentController::class,'checkout']);

    });
    //student only
    Route::middleware(['checkRole:5'])->group(function () {
        Route::post('/student/olympiad/register',[ParticipateController::class,'create']);  
        Route::get('/student/olympiad/{id}/registered',[ParticipateController::class,'show']);
        Route::get('/student/olympiad/{id}/lock-payment',[ParticipateController::class,'lock_register']);
        Route::get('/student/olympiad/your-olympiad',[ParticipateController::class,'showAll']);

        Route::get('/student/olympiad/{id}/checkout',[PaymentController::class,'checkout']);

    });

    //for all user
    Route::middleware(['checkRole:1,2,3'])->group(function () {
        
    });
    Route::middleware(['checkRole:1,2,3,4,5'])->group(function () {
        Route::get('/profile',[UserController::class,'show']);
        Route::put('/profile/update',[UserController::class,'update']);
        Route::delete('/profile/delete',[UserController::class,'show']);
        
        Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');

        Route::get('/post/{postid}/comment/',[CommentController::class,'show']);
        Route::post('/post/{postid}/comment/',[CommentController::class,'store']);
        
        //Payment success/cancel route
        Route::get('/success/{session_id}', [PaymentController::class, 'success']);
        Route::get('/cancel',[PaymentController::class,'cancel']);
    });
});

Route::get('/categories',[CategoryController::class, 'index'])->name('getCategories');

Route::get('/frontendmedia',[FrontendMediaController::class,'show']);
Route::get('frontendmedia/{page}',[FrontendMediaController::class, 'find']);

Route::post('/contact',[ContactForm::class, 'index']);

Route::get('/auth/google/redirect', [GoogleLoginController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);


// Route::get('/checkout',[PaymentController::class,'checkout']);
// Route::get('/cancel',[PaymentController::class,'cancel']);
// Incharge Creation 
// http://127.0.0.1:8000/register
// {
//     "name": "John Doe",
//     "email": "test@gmail.com",
//     "phone": "1234567890",
//     "gender": "Male",
//     "city": "New York",
//     "district": "Manhattan",
//     "pincode": 10001,
//     "school_id":3,
//     "state":"Andra Pradesh",
//     "password": "test@gmail.com",
//     "register_as_student":false
// }

// School create 
// http://127.0.0.1:8000/school/create with bearer token 
// {
//   "school_name":"school 1",
//   "school_landmark":"school 1 adderess",
//   "school_city":"school city 1",
//   "school_district":"Vijaywada",
//   "school_state":"Andra Pradesh",
//   "school_unique_code":"MTO00001",
//   "author_id":3
// }

// get schools 
// http://127.0.0.1:8000/school/

// get school
// http://127.0.0.1:8000/school/{id}

// student register 
// http://127.0.0.1:8000/register
// {
//     "name": "John Doe",
//     "email": "test11@gmail.com",
//     "aadhar_number":762123066333,
//     "phone": "1234567890",
//     "father": "Male",
//     "mother":"female",
//     "class": 7,
//     "dob":"2002-10-04",
//     "city": "New York",
//     "district": "Manhattan",
//     "pincode": 10001,
//     "school_id":2,
//     "state":"Andra Pradesh",
//     "password": "test11@gmail.com",
//      "register_as_student":true
// }


// olympiad register
// {
//   "user_id":4,
//   "school_id":2,
//   "olympiad_id":2,
//   "subjects":[2,3,4]
// }




// dummy record after pull
// cmd
// php artisan migrate:refresh

//  at phpmyadmin

// INSERT INTO users (name, email, role, district, state, pincode, password, created_at, updated_at)
// VALUES ('Super Admin', 'admin@gmail.com', 1, 'Vijaywada', 'Andhra Pradesh', '226020', '$2y$12$mDoAYccPb5lGYscGJ8zGi.3PFCI47tzS33I8Zvi8xSB09P3RhZeWC', NOW(), NOW());

//  at cmd
// php artisan db:seed


