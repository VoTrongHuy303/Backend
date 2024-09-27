<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;


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
    return view('welcome');
});
//role
Route::resource('roles', RoleController::class);
//user
Route::resource('users', UserController::class);


//login-logout
Route::get('/login', [UserController::class, 'indexlogin'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::get('auth/google', [UserController::class, 'googlelogin'])->name('logingoogle');
Route::get('auth/google/callback', [UserController::class, 'googlecallback'])->name('googlecallback');
Route::get('auth/facebook', [UserController::class, 'facebooklogin'])->name('loginfacebook');
Route::get('auth/facebook/callback', [UserController::class, 'facebookcallback'])->name('facebookcallback');
Route::get('password/forgot', [UserController::class, 'showForgotForm'])->name('password.forgot');
Route::post('password/forgot', [UserController::class, 'sendResetCode']);
Route::get('password/reset', [UserController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [UserController::class, 'resetPassword']);
Route::get('register', [UserController::class, 'showRegisterForm'])->name('register');
Route::post('register/send-code', [UserController::class, 'sendRegisterCode'])->name('register.sendCode');
Route::get('register/verify', [UserController::class, 'showVerifyForm'])->name('register.verify');
Route::post('register/verify-code', [UserController::class, 'verifyRegisterCode'])->name('register.verifyCode');
