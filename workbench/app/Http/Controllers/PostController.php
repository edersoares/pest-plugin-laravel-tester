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

    public function update(Request $request, string $id)
    {
        $model = Post::query()->findOrFail($id);

        $model->fill($request->all());
        $model->save();

        return $model;
    }

    public function destroy(string $id)
    {
        $model = Post::query()->findOrFail($id);

        $model->delete();

        return $model;
    }
}