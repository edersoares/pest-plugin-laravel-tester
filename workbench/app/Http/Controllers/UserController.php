<?php

declare(strict_types=1);

namespace Workbench\App\Http\Controllers;

use Workbench\App\Models\User;

class UserController
{
    public function destroy(string $id)
    {
        $model = User::query()->findOrFail($id);

        $model->delete();

        return $model;
    }
}
