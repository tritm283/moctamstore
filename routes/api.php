<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(base_path('routes/public.php'));
Route::prefix('admin/v1')->group(base_path('routes/admin.php'));
