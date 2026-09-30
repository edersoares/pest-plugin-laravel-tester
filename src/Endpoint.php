<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @mixin TestCase
 */
trait Endpoint
{
    use Eloquent;

    private string $endpoint;

    private ?string $wrap = null;

    /**
     * @var (Closure(array<string, mixed>): array<string, mixed>)|null
     */
    private ?Closure $transformPayload = null;

    /**
     * @var (Closure(array<array-key, mixed>): array<array-key, mixed>)|null
     */
    private ?Closure $transformResult = null;

    public function endpoint(string $endpoint): static
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function wrap(string $wrap): static
    {
        $this->wrap = $wrap;

        return $this;
    }

    /**
     * @param  Closure(array<string, mixed>): array<string, mixed>  $transformer
     */
    public function transformPayload(Closure $transformer): static
    {
        $this->transformPayload = $transformer;

        return $this;
    }

    /**
     * @param  Closure(array<array-key, mixed>): array<array-key, mixed>  $transformer
     */
    public function transformResult(Closure $transformer): static
    {
        $this->transformResult = $transformer;

        return $this;
    }

    /**
     * Creates a model and performs a GET request to the endpoint.
     *
     * @return TestResponse<Response>
     */
    public function doGetRequest(): TestResponse
    {
        $this->factory->createOne();

        return $this->getJson($this->endpoint)
            ->assertOk();
    }

    /**
     * Tests an index resource endpoint.
     *
     * @return TestResponse<Response>
     */
    public function toHaveIndexEndpoint(): TestResponse
    {
        $models = $this->factory->count(3)->create();

        $json = $this->wrapJson($models->toArray());

        return $this->getJson($this->endpoint)
            ->assertOk()
            ->assertJson($json);
    }

    /**
     * Tests a store resource endpoint.
     *
     * @return TestResponse<Response>
     */
    public function toHaveStoreEndpoint(): TestResponse
    {
        $modelAttributes = $this->factory->makeOne()->attributesToArray();

        $json = $this->wrapJson(
            $this->removeTimestamps($modelAttributes)
        );

        $payload = $this->preparePayload($modelAttributes);

        $response = $this->postJson($this->endpoint, $payload)
            ->assertCreated()
            ->assertJson($json);

        $this->assertDatabaseCount($this->class, 1);

        return $response;
    }

    /**
     * Tests a show resource endpoint.
     *
     * @return TestResponse<Response>
     */
    public function toHaveShowEndpoint(): TestResponse
    {
        $modelCreated = $this->factory->createOne();

        $json = $this->wrapJson(
            $this->removeTimestamps($modelCreated->attributesToArray())
        );

        return $this->getJson($this->endpoint.'/'.$this->keyOf($modelCreated))
            ->assertOk()
            ->assertJson($json);
    }

    /**
     * Tests a update resource endpoint.
     *
     * @return TestResponse<Response>
     */
    public function toHaveUpdateEndpoint(): TestResponse
    {
        $modelCreated = $this->factory->createOne();
        $modelUpdateAttributes = $this->factory->makeOne()->attributesToArray();

        $json = $this->wrapJson(
            $this->removeTimestamps($modelUpdateAttributes)
        );

        $payload = $this->preparePayload($modelUpdateAttributes);

        $response = $this->putJson($this->endpoint.'/'.$this->keyOf($modelCreated), $payload)
            ->assertOk()
            ->assertJson($json);

        $this->assertDatabaseMissing($modelCreated->getTable(), $this->removeTimestamps($modelCreated->attributesToArray()));
        $this->assertDatabaseHas($modelCreated->getTable(), $this->removeTimestamps($modelUpdateAttributes));
        $this->assertDatabaseCount($modelCreated->getTable(), 1);

        return $response;
    }

    /**
     * Tests a destroy resource endpoint.
     *
     * @return TestResponse<Response>
     */
    public function toHaveDestroyEndpoint(): TestResponse
    {
        $modelCreated = $this->factory->createOne();
        $attributes = $this->removeTimestamps($modelCreated->attributesToArray());

        $json = $this->wrapJson($attributes);

        $response = $this->deleteJson($this->endpoint.'/'.$this->keyOf($modelCreated))
            ->assertOk()
            ->assertJson($json);

        if ($this->usesSoftDeletes($modelCreated)) {
            $this->assertSoftDeleted($modelCreated->getTable(), deletedAtColumn: $this->deletedAtColumn($modelCreated));
            $this->assertDatabaseCount($modelCreated->getTable(), 1);
        } else {
            $this->assertDatabaseMissing($modelCreated->getTable(), $attributes);
            $this->assertDatabaseCount($modelCreated->getTable(), 0);
        }

        return $response;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    protected function wrapJson(array $data): array
    {
        if ($this->wrap === null) {
            return $this->prepareResult($data);
        }

        $result = [];

        Arr::set($result, $this->wrap, $this->prepareResult($data));

        return $result;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function preparePayload(array $data): array
    {
        if ($this->transformPayload === null) {
            return $data;
        }

        return ($this->transformPayload)($data);
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    protected function prepareResult(array $data): array
    {
        if ($this->transformResult === null) {
            return $data;
        }

        return ($this->transformResult)($data);
    }
}
