<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\TestCase;
use InvalidArgumentException;

/**
 * @mixin TestCase
 */
trait Eloquent
{
    /**
     * @var class-string<Model>
     */
    protected string $class;

    /**
     * @var Factory<Model>
     */
    protected Factory $factory;

    /**
     * Sets the Eloquent model.
     *
     * @param  class-string<Model>  $class
     */
    public function eloquent(string $class): static
    {
        $this->class = $class;
        $this->factory = $this->resolveFactory($class);

        return $this;
    }

    /**
     * Modifies the default factory.
     *
     * @param  callable(Factory<Model>): Factory<Model>  $callable
     */
    public function factory(callable $callable): static
    {
        $this->factory = $callable($this->factory);

        return $this;
    }

    /**
     * Tests if a model can be created.
     */
    public function toBeCreate(): static
    {
        $model = $this->factory->createOne();

        $this->assertDatabaseHas($model->getTable(), $this->removeTimestamps($model->getAttributes()));
        $this->assertDatabaseCount($model->getTable(), 1);

        return $this;
    }

    /**
     * Tests if a model can be updated.
     */
    public function toBeUpdate(): static
    {
        $modelCreated = $this->factory->createOne();
        $modelUpdateAttributes = $this->factory->makeOne()->attributesToArray();

        $modelUpdated = clone $modelCreated;

        $modelUpdated->fill($modelUpdateAttributes);
        $modelUpdated->save();

        $this->assertDatabaseMissing($modelUpdated->getTable(), $this->removeTimestamps($modelCreated->getAttributes()));
        $this->assertDatabaseHas($modelUpdated->getTable(), $this->removeTimestamps($modelUpdated->getAttributes()));
        $this->assertDatabaseCount($modelUpdated->getTable(), 1);

        return $this;
    }

    /**
     * Tests if a model can be deleted.
     */
    public function toBeDelete(): static
    {
        $model = $this->factory->createOne();

        $this->assertDatabaseHas($model->getTable(), $this->removeTimestamps($model->getAttributes()));

        $model->delete();

        if ($this->usesSoftDeletes($model)) {
            $this->assertSoftDeleted($model->getTable(), $this->removeTimestamps($model->getAttributes()), deletedAtColumn: $this->deletedAtColumn($model));
            $this->assertDatabaseCount($model->getTable(), 1);
        } else {
            $this->assertDatabaseMissing($model->getTable(), $this->removeTimestamps($model->getAttributes()));
            $this->assertDatabaseCount($model->getTable(), 0);
        }

        return $this;
    }

    /**
     * Resolves the factory of the model, honouring a custom `newFactory()`.
     *
     * @param  class-string<Model>  $class
     * @return Factory<Model>
     */
    protected function resolveFactory(string $class): Factory
    {
        $factory = method_exists($class, 'factory') ? $class::factory() : null;

        if (! $factory instanceof Factory) {
            throw new InvalidArgumentException("The model [$class] must use the HasFactory trait.");
        }

        return $factory;
    }

    /**
     * Returns the route key of the model as a string, ready to be used in a URL.
     */
    protected function keyOf(Model $model): string
    {
        $key = $model->getRouteKey();

        if (! is_scalar($key)) {
            throw new InvalidArgumentException('The model ['.$model::class.'] must have a scalar route key.');
        }

        return (string) $key;
    }

    /**
     * Checks if the model uses soft deletes.
     */
    protected function usesSoftDeletes(Model $model): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($model), true);
    }

    /**
     * Returns the soft delete column of the model.
     */
    protected function deletedAtColumn(Model $model): ?string
    {
        $column = method_exists($model, 'getDeletedAtColumn') ? $model->getDeletedAtColumn() : null;

        return is_string($column) ? $column : null;
    }

    /**
     * Removes timestamps from model to avoid false positive.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function removeTimestamps(array $attributes): array
    {
        unset($attributes['created_at'], $attributes['updated_at'], $attributes['deleted_at']);

        return $attributes;
    }
}
