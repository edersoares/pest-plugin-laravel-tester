<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\TestCase;

/**
 * @mixin TestCase
 */
trait Eloquent
{
    protected string $class;

    protected Factory $factory;

    public function eloquent(string $class): static
    {
        $this->class = $class;
        $this->factory = $class::factory();

        return $this;
    }

    public function factory(callable $callable): static
    {
        $this->factory = $callable($this->factory);

        return $this;
    }

    public function toBeCreate()
    {
        $model = $this->factory->create();

        $this->assertDatabaseHas($model->getTable(), $this->removeTimestamps($model->getAttributes()));
        $this->assertDatabaseCount($model->getTable(), 1);

        return test();
    }

    public function toBeUpdate()
    {
        $modelCreated = $this->factory->create();
        $modelUpdateAttributes = $this->factory->make()->toArray();

        $modelUpdated = clone $modelCreated;

        $modelUpdated->fill($modelUpdateAttributes);
        $modelUpdated->save();

        $this->assertDatabaseMissing($modelUpdated->getTable(), $this->removeTimestamps($modelCreated->getAttributes()));
        $this->assertDatabaseHas($modelUpdated->getTable(), $this->removeTimestamps($modelUpdated->getAttributes()));
        $this->assertDatabaseCount($modelUpdated->getTable(), 1);

        return test();
    }

    public function toBeDelete()
    {
        $model = $this->factory->create();

        $this->assertDatabaseHas($model->getTable(), $model->getAttributes());

        $model->delete();

        if (in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
            $this->assertSoftDeleted($model->getTable(), $this->removeTimestamps($model->getAttributes()), deletedAtColumn: $model->getDeletedAtColumn());
            $this->assertDatabaseCount($model->getTable(), 1);
        } else {
            $this->assertDatabaseMissing($model->getTable(), $this->removeTimestamps($model->getAttributes()));
            $this->assertDatabaseCount($model->getTable(), 0);
        }

        return test();
    }

    protected function removeTimestamps(array $attributes): array
    {
        if (array_key_exists('created_at', $attributes)) {
            unset($attributes['created_at']);
        }

        if (array_key_exists('updated_at', $attributes)) {
            unset($attributes['updated_at']);
        }

        if (array_key_exists('deleted_at', $attributes)) {
            unset($attributes['deleted_at']);
        }

        return $attributes;
    }
}
