<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Illuminate\Foundation\Testing\TestCase;

/**
 * @mixin TestCase
 */
trait Relation
{
    use Eloquent;

    /**
     * Tests a belongs to relation.
     *
     * @param  class-string  $class
     */
    public function toHaveBelongsToRelation(string $class, string $relation): static
    {
        $model = $this->factory->create();

        $this->assertInstanceOf($class, $model->getAttribute($relation));
        $this->assertDatabaseCount($model->getTable(), 1);
        $this->assertDatabaseCount($class, 1);

        return $this;
    }

    /**
     * Tests a has many relation.
     *
     * @param  class-string  $class
     */
    public function toHaveHasManyRelation(string $class, string $relation): static
    {
        $model = $this->factory
            ->has($class::factory(), $relation)
            ->create();

        $this->assertContainsOnlyInstancesOf($class, $model->getAttribute($relation));
        $this->assertCount(1, $model->getAttribute($relation));

        return $this;
    }

    /**
     * Tests a has one relation.
     *
     * @param  class-string  $class
     */
    public function toHaveHasOneRelation(string $class, string $relation): static
    {
        $model = $this->factory
            ->has($class::factory(), $relation)
            ->create();

        $this->assertInstanceOf($class, $model->getAttribute($relation));

        return $this;
    }

    /**
     * Tests a has many through relation.
     *
     * @param  class-string  $class
     * @param  class-string  $through
     */
    public function toHaveHasManyThroughRelation(string $class, string $through, string $relation): static
    {
        $model = $this->factory->create();

        $through::factory()
            ->for($model)
            ->has($class::factory())
            ->create();

        $this->assertContainsOnlyInstancesOf($class, $model->getAttribute($relation));
        $this->assertCount(1, $model->getAttribute($relation));

        return $this;
    }
}
