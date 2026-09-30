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

    public function show(User $user)
    {
        return [
            'user' => $user,
        ];
    }

    public function destroy(User $user)
    {
        $user->delete();

        return $user;
    }
}
