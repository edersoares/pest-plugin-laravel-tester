<?php

declare(strict_types=1);

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\PostController;
use Workbench\App\Http\Controllers\UserController;

Route::middleware(SubstituteBindings::class)->group(function (): void {
    Route::apiResource('/api/post', PostController::class);
    Route::apiResource('/api/user', UserController::class);
});
