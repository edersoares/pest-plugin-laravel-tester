<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\PostController;

Route::apiResource('/api/post', PostController::class);
