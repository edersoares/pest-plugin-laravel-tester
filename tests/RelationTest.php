<?php

declare(strict_types=1);

use PHPUnit\Framework\AssertionFailedError;
use Workbench\App\Models\Comment;
use Workbench\App\Models\Post;
use Workbench\App\Models\User;

describe('Relation', function () {
    beforeEach()->eloquent(Post::class);

    test()->toHaveBelongsToRelation(User::class, 'user');
    test()->toHaveHasManyRelation(Comment::class, 'comments');
    test()->toHaveHasOneRelation(Comment::class, 'latestComment');
});

describe('HasManyThrough relation', function () {
    beforeEach()->eloquent(User::class);

    test()->toHaveHasManyThroughRelation(Comment::class, Post::class, 'comments');
});

describe('Relation failures', function () {
    beforeEach()->eloquent(Post::class);

    test('toHaveBelongsToRelation fails when the relation returns another class', function () {
        expect(fn () => $this->toHaveBelongsToRelation(Comment::class, 'user'))
            ->toThrow(AssertionFailedError::class);
    });

    test('toHaveHasOneRelation fails when the relation returns a collection', function () {
        expect(fn () => $this->toHaveHasOneRelation(Comment::class, 'comments'))
            ->toThrow(AssertionFailedError::class);
    });
});
