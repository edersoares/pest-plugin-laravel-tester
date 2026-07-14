<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Closure;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Testing\TestResponse;

trait Endpoint
{
    use Eloquent;

    private string $endpoint;

    private ?string $wrap = null;

    private ?Closure $transformPayload = null;

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

    public function transformPayload(Closure $transformer): static
    {
        $this->transformPayload = $transformer;

        return $this;
    }

    public function transformResult(Closure $transformer): static
    {
        $this->transformResult = $transformer;

        return $this;
    }

    public function doGetRequest()
    {
        $this->factory->create();

        return $this->getJson($this->endpoint)
            ->assertOk();
    }

    /**
     * Tests an index resource endpoint.
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
     */
    public function toHaveStoreEndpoint(): TestResponse
    {
        $modelAttributes = $this->factory->make()->toArray();

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
     */
    public function toHaveShowEndpoint(): TestResponse
    {
        $modelCreated = $this->factory->create();

        $json = $this->wrapJson(
            $this->removeTimestamps($modelCreated->toArray())
        );

        return $this->getJson($this->endpoint.'/'.$modelCreated->getKey())
            ->assertOk()
            ->assertJson($json);
    }

    /**
     * Tests a update resource endpoint.
     */
    public function toHaveUpdateEndpoint(): TestResponse
    {
        $modelCreated = $this->factory->create();
        $modelUpdateAttributes = $this->factory->make()->toArray();

        $json = $this->wrapJson(
            $this->removeTimestamps($modelUpdateAttributes)
        );

        $payload = $this->preparePayload($modelUpdateAttributes);

        $response = $this->putJson($this->endpoint.'/'.$modelCreated->getKey(), $payload)
            ->assertOk()
            ->assertJson($json);

        $this->assertDatabaseMissing($modelCreated->getTable(), $this->removeTimestamps($modelCreated->toArray()));
        $this->assertDatabaseHas($modelCreated->getTable(), $this->removeTimestamps($modelUpdateAttributes));
        $this->assertDatabaseCount($modelCreated->getTable(), 1);

        return $response;
    }

    /**
     * Tests a destroy resource endpoint.
     */
    public function toHaveDestroyEndpoint(): TestResponse
    {
        $modelCreated = $this->factory->create();
        $attributes = $this->removeTimestamps($modelCreated->toArray());

        $json = $this->wrapJson(
            $this->removeTimestamps($attributes)
        );

        $response = $this->deleteJson($this->endpoint.'/'.$modelCreated->getKey())
            ->assertOk()
            ->assertJson($json);

        if (in_array(SoftDeletes::class, class_uses_recursive($modelCreated), true)) {
            $this->assertSoftDeleted($modelCreated->getTable(), deletedAtColumn: $modelCreated->getDeletedAtColumn());
            $this->assertDatabaseCount($modelCreated->getTable(), 1);
        } else {
            $this->assertDatabaseMissing($modelCreated->getTable(), $this->removeTimestamps($modelCreated->toArray()));
            $this->assertDatabaseCount($modelCreated->getTable(), 0);
        }

        return $response;
    }

    protected function wrapJson(array $data): array
    {
        $result = [];

        if ($this->wrap) {
            data_set($result, $this->wrap, $this->prepareResult($data));
        } else {
            return $this->prepareResult($data);
        }

        return $result;
    }

    protected function preparePayload(array $data): array
    {
        if (empty($transformer = $this->transformPayload)) {
            return $data;
        }

        return $transformer($data);
    }

    protected function prepareResult(array $data): array
    {
        if (empty($transformer = $this->transformResult)) {
            return $data;
        }

        return $transformer($data);
    }
}
