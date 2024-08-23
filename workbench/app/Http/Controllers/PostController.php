<?php

declare(strict_types=1);

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\Request;
use Workbench\App\Models\Post;

class PostController
{
    public function store(Request $request)
    {
        return Post::query()->create($request->all());
    }
}
