<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Factories\Factory;
use PHPUnit\Framework\AssertionFailedError;
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

describe('Eloquent failures', function () {
    test('toBeCreate fails when the database differs from the model', function () {
        Post::created(fn (Post $post) => Post::query()->whereKey($post)->update(['title' => 'Overwritten']));

        expect(fn () => $this->eloquent(Post::class)->toBeCreate())
            ->toThrow(AssertionFailedError::class);
    });

    test('toBeUpdate fails when the model ignores the update', function () {
        Post::updating(fn () => false);

        expect(fn () => $this->eloquent(Post::class)->toBeUpdate())
            ->toThrow(AssertionFailedError::class);
    });

    test('toBeDelete fails when the model prevents the deletion', function () {
        Post::deleting(fn () => false);

        expect(fn () => $this->eloquent(Post::class)->toBeDelete())
            ->toThrow(AssertionFailedError::class);
    });
});
