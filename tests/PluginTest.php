<?php

declare(strict_types=1);

use Workbench\App\Models\Post;

describe('Post', function () {
    beforeEach()->eloquent(Post::class);

    test()->toBeCreate();
    test()->toBeUpdate();
    test()->toBeDelete();
});
