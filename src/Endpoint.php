<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Illuminate\Database\Eloquent\SoftDeletes;

trait Endpoint
{
    use Eloquent;

    private string $endpoint;

    private ?string $wrap = null;

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

    public function toHaveIndexEndpoint()
    {
        $models = $this->factory->count(3)->create();

        $json = $this->wrapJson($models->toArray());

        return $this->getJson($this->endpoint)
            ->assertOk()
            ->assertJson($json);
    }

    public function toHaveStoreEndpoint()
    {
        $modelAttributes = $this->factory->make()->toArray();

        $json = $this->wrapJson(
            $this->removeTimestamps($modelAttributes)
        );

        $response = $this->postJson($this->endpoint, $modelAttributes)
            ->assertCreated()
            ->assertJson($json);

        $this->assertDatabaseCount($this->class, 1);

        return $response;
    }

    public function toHaveShowEndpoint()
    {
        $modelCreated = $this->factory->create();

        $json = $this->wrapJson(
            $this->removeTimestamps($modelCreated->getAttributes())
        );

        return $this->getJson($this->endpoint.'/'.$modelCreated->getKey())
            ->assertOk()
            ->assertJson($json);
    }

    public function toHaveUpdateEndpoint()
    {
        $modelCreated = $this->factory->create();
        $modelUpdateAttributes = $this->factory->make()->toArray();

        $json = $this->wrapJson(
            $this->removeTimestamps($modelUpdateAttributes)
        );

        $response = $this->putJson($this->endpoint.'/'.$modelCreated->getKey(), $modelUpdateAttributes)
            ->assertOk()
            ->assertJson($json);

        $this->assertDatabaseMissing($modelCreated->getTable(), $this->removeTimestamps($modelCreated->getAttributes()));
        $this->assertDatabaseHas($modelCreated->getTable(), $this->removeTimestamps($modelUpdateAttributes));
        $this->assertDatabaseCount($modelCreated->getTable(), 1);

        return $response;
    }

    public function toHaveDestroyEndpoint()
    {
        $modelCreated = $this->factory->create();
        $attributes = $this->removeTimestamps($modelCreated->getAttributes());

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
            $this->assertDatabaseMissing($modelCreated->getTable(), $this->removeTimestamps($modelCreated->getAttributes()));
            $this->assertDatabaseCount($modelCreated->getTable(), 0);
        }

        return $response;
    }

    protected function wrapJson(array $data): array
    {
        $result = [];

        if ($this->wrap) {
            data_set($result, $this->wrap, $data);
        } else {
            return $data;
        }

        return $result;
    }
}
