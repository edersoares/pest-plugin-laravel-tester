<?php

declare(strict_types=1);

namespace Workbench\App\Http\Controllers;

use Workbench\App\Models\User;

class UserController
{
    public function index()
    {
        if (request('nowrap')) {
            return User::query()->get();
        } else {
            return User::query()->paginate();
        }
    }

    public function show(string $id)
    {
        return [
            'user' => User::query()->findOrFail($id),
        ];
    }

    public function destroy(string $id)
    {
        $model = User::query()->findOrFail($id);

        $model->delete();

        return $model;
    }
}
