<?php

declare(strict_types=1);

use Workbench\App\Models\Post;

describe('Eloquent', function () {
    beforeEach()->eloquent(Post::class);

    test()->toBeCreate();
    test()->toBeUpdate();
    test()->toBeDelete();
});

describe('Custom factory', function () {
    beforeEach()->eloquent(Post::class);
    beforeEach()->factory(fn ($factory) => $factory->state([
        'title' => 'Laravel Tester',
    ]));

    test()->toBeCreate()->assertDatabaseHas(Post::class, [
        'title' => 'Laravel Tester',
    ]);
});
