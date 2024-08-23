<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Illuminate\Database\Eloquent\SoftDeletes;

trait Endpoint
{
    private string $endpoint;

    public function endpoint(string $endpoint): static
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function toHaveStoreEndpoint()
    {
        $modelAttributes = $this->factory->make()->toArray();

        $response = $this->postJson($this->endpoint, $modelAttributes)
            ->assertCreated()
            ->assertJson($modelAttributes);

        $this->assertDatabaseCount($this->class, 1);

        return $response;
    }

    public function toHaveUpdateEndpoint()
    {
        $modelCreated = $this->factory->create();
        $modelUpdateAttributes = $this->factory->make()->toArray();

        $response = $this->putJson($this->endpoint.'/'.$modelCreated->getKey(), $modelUpdateAttributes)
            ->assertOk()
            ->assertJson($modelUpdateAttributes);

        $this->assertDatabaseMissing($modelCreated->getTable(), $this->removeTimestamps($modelCreated->getAttributes()));
        $this->assertDatabaseHas($modelCreated->getTable(), $this->removeTimestamps($modelUpdateAttributes));
        $this->assertDatabaseCount($modelCreated->getTable(), 1);

        return $response;
    }

    public function toHaveDestroyEndpoint()
    {
        $modelCreated = $this->factory->create();
        $attributes = $this->removeTimestamps($modelCreated->getAttributes());

        $response = $this->deleteJson($this->endpoint.'/'.$modelCreated->getKey())
            ->assertOk()
            ->assertJson($attributes);

        if (in_array(SoftDeletes::class, class_uses_recursive($modelCreated), true)) {
            $this->assertSoftDeleted($modelCreated->getTable(), deletedAtColumn: $modelCreated->getDeletedAtColumn());
            $this->assertDatabaseCount($modelCreated->getTable(), 1);
        } else {
            $this->assertDatabaseMissing($modelCreated->getTable(), $this->removeTimestamps($modelCreated->getAttributes()));
            $this->assertDatabaseCount($modelCreated->getTable(), 0);
        }

        return $response;
    }
}
