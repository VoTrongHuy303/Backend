<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;



// Routes từ nhánh HEAD
Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api/users', 'middleware' => ['auth', 'checkRole:admin']], function() {
    // Các route chỉ dành cho admin
    
});
// Quản lý roles
Route::resource('roles', RoleController::class);
// Quản lý users
Route::resource('api/roles', RoleController::class);

// Quản lý users
Route::group(['prefix' => 'api/users'], function() {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::post('/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/show/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/update/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/delete/{user}', [UserController::class, 'destroy'])->name('users.delete');
});

// Role API routes
Route::group(['prefix' => 'api/roles'], function () {
    Route::get('/', [RoleController::class, 'index'])->name('Role.index');
    Route::post('/store', [RoleController::class, 'store'])->name('Role.store');
    Route::get('/show/{role}', [RoleController::class, 'show'])->name('Role.show');
    Route::put('/update/{role}', [RoleController::class, 'update'])->name('Role.update');
    Route::delete('/delete/{role}', [RoleController::class, 'destroy'])->name('Role.destroy');
});

// Đăng nhập - đăng xuất
Route::post('/api/login', [UserController::class, 'login'])->name('api.login');
Route::post('/api/logout', [UserController::class, 'logout'])->name('api.logout');
Route::get('/api/auth/google', [UserController::class, 'googlelogin'])->name('api.logingoogle');
Route::get('/api/auth/google/callback', [UserController::class, 'googlecallback'])->name('api.googlecallback');

// Đăng ký
Route::group(['prefix' => 'api/register'], function() {
    Route::post('/send-code', [UserController::class, 'sendRegisterCode'])->name('api.register.sendCode');
    Route::post('/verify-code', [UserController::class, 'verifyRegisterCode'])->name('api.register.verifyCode');
});

// Quên mật khẩu
Route::group(['prefix' => 'api/password'], function() {
    Route::post('/forgot', [UserController::class, 'sendResetCode'])->name('api.password.forgot');
    Route::post('/reset', [UserController::class, 'resetPassword'])->name('api.password.reset');
});



// Routes từ nhánh origin/main
route::group([
    'prefix' => 'api/product'
],function(){
    Route::get('/', [ProductController::class, 'index'])->name('Product.index');
    Route::get('/create', [ProductController::class, 'create'])->name('Product.create');
    Route::post('/store', [ProductController::class, 'store'])->name('Product.store');
    Route::get('/show/{productVariant}', [ProductController::class, 'show'])->name('Product.show');
    Route::get('/shop', [ProductController::class, 'shop'])->name('Product.shop');
    Route::get('/edit/{product}', [ProductController::class, 'edit'])->name('Product.edit');
    Route::put('/update/{product}', [ProductController::class, 'update'])->name('Product.update');
    Route::delete('/delete/{product}', [ProductController::class, 'destroy'])->name('Product.delete');
    Route::get('/getProductByTag/{tag}', [ProductController::class, 'getProductByTag'])->name('Product.getProductByTag');
});

route::group([
    'prefix' => 'api/category'
],function(){
    Route::get('/', [CategoryController::class, 'index'])->name('Category.index');
    Route::get('/create', [CategoryController::class, 'create'])->name('Category.create');
    Route::post('/store', [CategoryController::class, 'store'])->name('Category.store');
    Route::get('/edit/{category}', [CategoryController::class, 'edit'])->name('Category.edit');
    Route::put('/update/{category}', [CategoryController::class, 'update'])->name('Category.update');
    Route::delete('/delete/{category}', [CategoryController::class, 'destroy'])->name('Category.delete');
    Route::get('/getCategoryByTag/{tag}', [CategoryController::class, 'getCategoryByTag'])->name('Category.getCategoryByTag');
});

route::group([
    'prefix' => 'api/brand',
],function(){
    Route::get('/', [BrandController::class, 'index'])->name('Brand.index');
    Route::get('/create', [BrandController::class, 'create'])->name('Brand.create');
    Route::post('/store', [BrandController::class, 'store'])->name('Brand.store');
    Route::get('/edit/{brand}', [BrandController::class, 'edit'])->name('Brand.edit');
    Route::put('/update/{brand}', [BrandController::class, 'update'])->name('Brand.update');
    Route::delete('/delete/{brand}', [BrandController::class, 'destroy'])->name('Brand.delete');
    Route::get('/getBrandByTag/{tag}', [BrandController::class, 'getBrandByTag'])->name('Brand.getBrandByTag');
});

Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});
