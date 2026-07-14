# Pest Plugin Laravel Tester

Test Laravel Eloquent models, relationships, API endpoints and validation rules with near-zero boilerplate using Pest's higher-order syntax.

```php
describe('Post', function () {
    beforeEach()->eloquent(Post::class)->endpoint('/api/post');

    test()->toBeCreate();
    test()->toBeUpdate();
    test()->toBeDelete();

    test()->toHaveBelongsToRelation(User::class, 'user');
    test()->toHaveHasManyRelation(Comment::class, 'comments');

    test()->toHaveIndexEndpoint();
    test()->toHaveStoreEndpoint();
    test()->toHaveShowEndpoint();
    test()->toHaveUpdateEndpoint();
    test()->toHaveDestroyEndpoint();

    test()->toValidateRequired('title');
    test()->toValidateMin('title', 10);
    test()->toValidateMax('title', 200);
});
```

## Installation

```bash
composer require --dev dex/pest-plugin-laravel-tester
```

Add the `Tester` trait to your `tests/Pest.php`:

```php
use Dex\Pest\Plugin\Laravel\Tester\Tester;

uses(Tester::class)->in(__DIR__);
```

## Eloquent CRUD

Assert that a model can be created, updated, and deleted using its factory. Soft deletes are detected automatically.

```php
describe('Post', function () {
    beforeEach()->eloquent(Post::class);

    test()->toBeCreate();
    test()->toBeUpdate();
    test()->toBeDelete(); // uses assertSoftDeleted() when the model uses SoftDeletes
});
```

Customize the factory state for a specific test:

```php
describe('Post', function () {
    beforeEach()->eloquent(Post::class);
    beforeEach()->factory(fn ($factory) => $factory->state(['status' => 'published']));

    test()->toBeCreate()->assertDatabaseHas(Post::class, ['status' => 'published']);
});
```

## Relationships

```php
describe('Post relationships', function () {
    beforeEach()->eloquent(Post::class);

    test()->toHaveBelongsToRelation(User::class, 'user');
    test()->toHaveHasManyRelation(Comment::class, 'comments');
    test()->toHaveHasOneRelation(Comment::class, 'latestComment');
});

describe('User relationships', function () {
    beforeEach()->eloquent(User::class);

    test()->toHaveHasManyThroughRelation(Comment::class, Post::class, 'comments');
});
```

## REST API Endpoints

Assert the full CRUD cycle of an API resource, including HTTP status codes and database state after each operation.

```php
describe('Post API', function () {
    beforeEach()->eloquent(Post::class)->endpoint('/api/post');

    test()->toHaveIndexEndpoint();   // GET    /api/post       → 200
    test()->toHaveStoreEndpoint();   // POST   /api/post       → 201 + assertDatabaseCount(1)
    test()->toHaveShowEndpoint();    // GET    /api/post/{id}  → 200
    test()->toHaveUpdateEndpoint();  // PUT    /api/post/{id}  → 200 + assertDatabaseHas
    test()->toHaveDestroyEndpoint(); // DELETE /api/post/{id}  → 200 + assertDatabaseMissing
});
```

When the response wraps data under a key (e.g., Laravel's API resources or pagination):

```php
test()->wrap('data')->toHaveIndexEndpoint();
test()->wrap('post')->toHaveShowEndpoint();
```

Transform the payload before sending or the response before asserting:

```php
beforeEach()->transformPayload(fn ($data) => collect($data)->except('computed')->all());
beforeEach()->transformResult(fn ($data) => collect($data)->except('computed')->all());
```

## Validation

Each method tests both the `store` (POST) and `update` (PUT) endpoints, asserting a 422 with the appropriate validation error.

```php
describe('Post validation', function () {
    beforeEach()->eloquent(Post::class)->endpoint('/api/post');

    test()->toValidateRequired('title');
    test()->toValidateMin('title', 10);
    test()->toValidateMax('title', 200);
    test()->toValidateSize('code', 8);
});
```

## License

Pest Plugin Laravel Tester is open-sourced software licensed under the [MIT license](LICENSE.md).
