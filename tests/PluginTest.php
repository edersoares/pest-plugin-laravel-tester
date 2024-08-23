<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Post;
use Workbench\App\Models\User;

describe('Eloquent', function () {
    beforeEach()->eloquent(Post::class);

    test()->toBeCreate();
    test()->toBeUpdate();
    test()->toBeDelete();
});

describe('Custom factory', function () {
    beforeEach()->eloquent(Post::class);
    beforeEach()->factory(fn (Factory $factory) => $factory->state([
        'title' => 'Laravel Tester',
    ]));

    test()->toBeCreate()->assertDatabaseHas(Post::class, [
        'title' => 'Laravel Tester',
    ]);
});

describe('Soft deletes', function () {
    beforeEach()->eloquent(User::class);

    test()->toBeDelete();
});
