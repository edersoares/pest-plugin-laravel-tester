<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Illuminate\Database\Eloquent\Model;
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
     * @param  class-string<Model>  $class
     */
    public function toHaveBelongsToRelation(string $class, string $relation): static
    {
        $model = $this->factory->createOne();

        $this->assertInstanceOf($class, $model->getAttribute($relation));
        $this->assertDatabaseCount($model->getTable(), 1);
        $this->assertDatabaseCount($class, 1);

        return $this;
    }

    /**
     * Tests a has many relation.
     *
     * @param  class-string<Model>  $class
     */
    public function toHaveHasManyRelation(string $class, string $relation): static
    {
        $model = $this->factory
            ->has($this->resolveFactory($class), $relation)
            ->createOne();

        $related = $model->getAttribute($relation);

        $this->assertIsIterable($related);
        $this->assertContainsOnlyInstancesOf($class, $related);
        $this->assertCount(1, $related);

        return $this;
    }

    /**
     * Tests a has one relation.
     *
     * @param  class-string<Model>  $class
     */
    public function toHaveHasOneRelation(string $class, string $relation): static
    {
        $model = $this->factory
            ->has($this->resolveFactory($class), $relation)
            ->createOne();

        $this->assertInstanceOf($class, $model->getAttribute($relation));

        return $this;
    }

    /**
     * Tests a has many through relation.
     *
     * @param  class-string<Model>  $class
     * @param  class-string<Model>  $through
     */
    public function toHaveHasManyThroughRelation(string $class, string $through, string $relation): static
    {
        $model = $this->factory->createOne();

        $this->resolveFactory($through)
            ->for($model)
            ->has($this->resolveFactory($class))
            ->create();

        $related = $model->getAttribute($relation);

        $this->assertIsIterable($related);
        $this->assertContainsOnlyInstancesOf($class, $related);
        $this->assertCount(1, $related);

        return $this;
    }
}
