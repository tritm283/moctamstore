<?php

use Illuminate\Support\Facades\Route;
Route::get('/', fn () => response()->json(['service' => 'ecommerce-cms-api', 'status' => 'ok']));
