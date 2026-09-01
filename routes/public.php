<?php
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function(){
    Route::post('auth/register',[AuthController::class,'register']);
    Route::post('auth/login',[AuthController::class,'login']);
    Route::post('auth/social',[AuthController::class,'social']);
    Route::get('home',[HomeController::class,'index']); Route::get('menus',[HomeController::class,'menus']);
    Route::get('categories',[CatalogController::class,'categories']); Route::get('products',[CatalogController::class,'products']); Route::get('products/{idOrSlug}',[CatalogController::class,'product']);
    Route::get('article-categories',[CatalogController::class,'articleCategories']); Route::get('articles',[CatalogController::class,'articles']); Route::get('articles/{slug}',[CatalogController::class,'article']);
});
Route::middleware(['auth:sanctum','throttle:120,1'])->group(function(){
    Route::get('auth/me',[AuthController::class,'me']); Route::post('auth/logout',[AuthController::class,'logout']);
    Route::patch('profile',[ProfileController::class,'update']); Route::post('profile/avatar',[ProfileController::class,'avatar']);
    Route::get('addresses',[AddressController::class,'index']); Route::post('addresses',[AddressController::class,'store']); Route::put('addresses/{address}',[AddressController::class,'update']); Route::delete('addresses/{address}',[AddressController::class,'destroy']); Route::post('addresses/{address}/default',[AddressController::class,'setDefault']);
    Route::get('wishlist',[WishlistController::class,'index']); Route::post('wishlist/toggle',[WishlistController::class,'toggle']); Route::delete('wishlist/{productId}',[WishlistController::class,'destroy']);
    Route::get('cart',[CartController::class,'show']); Route::post('cart/items',[CartController::class,'add']); Route::patch('cart/items/{item}',[CartController::class,'update']); Route::delete('cart/items/{item}',[CartController::class,'destroy']);
    Route::post('checkout',[CheckoutController::class,'store']); Route::get('orders',[OrderController::class,'index']); Route::get('orders/{order}',[OrderController::class,'show']);
});
