<?php
use App\Http\Controllers\Admin\V1\ArticleCategoryController;
use App\Http\Controllers\Admin\V1\ArticleController;
use App\Http\Controllers\Admin\V1\AuthController;
use App\Http\Controllers\Admin\V1\CategoryController;
use App\Http\Controllers\Admin\V1\DashboardController;
use App\Http\Controllers\Admin\V1\HomepageSectionController;
use App\Http\Controllers\Admin\V1\MediaController;
use App\Http\Controllers\Admin\V1\MenuController;
use App\Http\Controllers\Admin\V1\OrderController;
use App\Http\Controllers\Admin\V1\PaymentController;
use App\Http\Controllers\Admin\V1\ProductController;
use App\Http\Controllers\Admin\V1\UserController;
use Illuminate\Support\Facades\Route;
Route::post('auth/login',[AuthController::class,'login'])->middleware('throttle:10,1');
Route::middleware(['auth:sanctum','admin','throttle:180,1'])->group(function(){
    Route::get('auth/me',[AuthController::class,'me']); Route::post('auth/logout',[AuthController::class,'logout']); Route::get('dashboard',[DashboardController::class,'index']);
    Route::apiResource('media',MediaController::class)->only(['index','store','destroy'])->parameters(['media'=>'medium']);
    Route::apiResource('categories',CategoryController::class); Route::apiResource('products',ProductController::class); Route::post('products/{product}/image',[ProductController::class,'image']);
    Route::apiResource('article-categories',ArticleCategoryController::class)->parameters(['article-categories'=>'articleCategory']); Route::apiResource('articles',ArticleController::class); Route::post('articles/{article}/thumbnail',[ArticleController::class,'thumbnail']);
    Route::apiResource('homepage-sections',HomepageSectionController::class)->parameters(['homepage-sections'=>'homepageSection']); Route::post('homepage-sections/reorder',[HomepageSectionController::class,'reorder']);
    Route::apiResource('menus',MenuController::class); Route::post('menus/reorder',[MenuController::class,'reorder']);
    Route::get('users',[UserController::class,'index']); Route::get('users/{user}',[UserController::class,'show']); Route::patch('users/{user}',[UserController::class,'update']);
    Route::get('orders',[OrderController::class,'index']); Route::get('orders/{order}',[OrderController::class,'show']); Route::patch('orders/{order}/status',[OrderController::class,'status']);
    Route::get('payments',[PaymentController::class,'index']); Route::get('payments/{payment}',[PaymentController::class,'show']); Route::patch('payments/{payment}/status',[PaymentController::class,'status']);
});
