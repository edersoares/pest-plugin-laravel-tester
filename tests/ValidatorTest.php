<?php

declare(strict_types=1);

use PHPUnit\Framework\AssertionFailedError;
use Workbench\App\Models\Post;

describe('Validator', function () {
    beforeEach()->eloquent(Post::class);
    beforeEach()->endpoint('/api/post');

    test()->toValidateRequired('title');
    test()->toValidateMin('title', 10);
    test()->toValidateMax('title', 200);
    test()->toValidateSize('short', 8);
});

describe('Validator failures', function () {
    beforeEach()->eloquent(Post::class);
    beforeEach()->endpoint('/api/post');

    test('toValidateRequired fails when the attribute is optional', function () {
        expect(fn () => $this->toValidateRequired('status'))
            ->toThrow(AssertionFailedError::class);
    });

    test('toValidateMin fails when the rule allows a shorter value', function () {
        expect(fn () => $this->toValidateMin('title', 20))
            ->toThrow(AssertionFailedError::class);
    });

    test('toValidateMax fails when the rule allows a longer value', function () {
        expect(fn () => $this->toValidateMax('title', 100))
            ->toThrow(AssertionFailedError::class);
    });

    test('toValidateSize fails when the attribute has no size rule', function () {
        expect(fn () => $this->toValidateSize('title', 50))
            ->toThrow(AssertionFailedError::class);
    });
});
