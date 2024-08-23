<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\PostController;
use Workbench\App\Http\Controllers\UserController;

Route::apiResource('/api/post', PostController::class);
Route::apiResource('/api/user', UserController::class);
