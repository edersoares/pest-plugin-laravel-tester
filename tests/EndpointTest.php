<?php

declare(strict_types=1);

use PHPUnit\Framework\AssertionFailedError;
use Workbench\App\Models\Post;
use Workbench\App\Models\User;

describe('Endpoint', function () {
    beforeEach()->eloquent(Post::class);
    beforeEach()->endpoint('/api/post');

    test()->toHaveIndexEndpoint();
    test()->toHaveStoreEndpoint();
    test()->toHaveShowEndpoint();
    test()->toHaveUpdateEndpoint();
    test()->toHaveDestroyEndpoint();
});

describe('Endpoint and soft deletes', function () {
    beforeEach()->eloquent(User::class);
    beforeEach()->endpoint('/api/user');

    test()->toHaveDestroyEndpoint();
});

describe('Endpoint and custom route key', function () {
    beforeEach()->eloquent(User::class);
    beforeEach()->endpoint('/api/user');

    test()->wrap('user')->toHaveShowEndpoint();
    test()->toHaveDestroyEndpoint();

    test('requests use the route key instead of the primary key', function () {
        $user = User::factory()->createOne();

        $this->getJson('/api/user/'.$user->getKey())->assertNotFound();
        $this->getJson('/api/user/'.$user->slug)->assertOk();
    });
});

describe('Endpoint and data wrapping', function () {
    beforeEach()->eloquent(User::class);
    beforeEach()->endpoint('/api/user');

    test()->wrap('data')->toHaveIndexEndpoint();
    test()->endpoint('/api/user?nowrap=true')->toHaveIndexEndpoint();
    test()->wrap('user')->toHaveShowEndpoint();
});

describe('Endpoint and transformers', function () {
    beforeEach()->eloquent(Post::class);
    beforeEach()->endpoint('/api/post');
    beforeEach()->transformPayload(fn (array $payload) => [...$payload, 'ignored' => 'by fillable']);
    beforeEach()->transformResult(fn (array $result) => collect($result)->except('status')->all());

    test()->toHaveIndexEndpoint();
    test()->toHaveStoreEndpoint();
    test()->toHaveShowEndpoint();
    test()->toHaveUpdateEndpoint();
    test()->toHaveDestroyEndpoint();
});

describe('Endpoint GET request', function () {
    beforeEach()->eloquent(Post::class);
    beforeEach()->endpoint('/api/post');

    test()->doGetRequest()->assertJsonCount(1);
});

describe('Endpoint failures', function () {
    beforeEach()->eloquent(Post::class);

    test('wrap rejects an empty key', function () {
        expect(fn () => $this->wrap(''))
            ->toThrow(InvalidArgumentException::class, 'The wrap key must not be empty.');
    });

    test('toHaveIndexEndpoint fails when the route does not exist', function () {
        expect(fn () => $this->endpoint('/api/missing')->toHaveIndexEndpoint())
            ->toThrow(AssertionFailedError::class);
    });

    test('toHaveStoreEndpoint fails when the route does not exist', function () {
        expect(fn () => $this->endpoint('/api/missing')->toHaveStoreEndpoint())
            ->toThrow(AssertionFailedError::class);
    });

    test('toHaveShowEndpoint fails when the route does not exist', function () {
        expect(fn () => $this->endpoint('/api/missing')->toHaveShowEndpoint())
            ->toThrow(AssertionFailedError::class);
    });

    test('toHaveUpdateEndpoint fails when the route does not exist', function () {
        expect(fn () => $this->endpoint('/api/missing')->toHaveUpdateEndpoint())
            ->toThrow(AssertionFailedError::class);
    });

    test('toHaveDestroyEndpoint fails when the route does not exist', function () {
        expect(fn () => $this->endpoint('/api/missing')->toHaveDestroyEndpoint())
            ->toThrow(AssertionFailedError::class);
    });

    test('toHaveIndexEndpoint fails when the response is not wrapped as expected', function () {
        expect(fn () => $this->endpoint('/api/post')->wrap('data')->toHaveIndexEndpoint())
            ->toThrow(AssertionFailedError::class);
    });
});
