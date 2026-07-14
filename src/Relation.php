<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Pest\PendingCalls\TestCall;
use Pest\Support\HigherOrderTapProxy;

trait Relation
{
    use Eloquent;

    /**
     * Tests a belongs to relation.
     *
     * @param class-string $class
     */
    public function toHaveBelongsToRelation(string $class, string $relation): HigherOrderTapProxy|TestCall
    {
        $model = $this->factory->create();

        $this->assertInstanceOf($class, $model->getAttribute($relation));
        $this->assertDatabaseCount($model->getTable(), 1);
        $this->assertDatabaseCount($class, 1);

        return test();
    }

    /**
     * Tests a has many relation.
     *
     * @param class-string $class
     */
    public function toHaveHasManyRelation(string $class, string $relation): HigherOrderTapProxy|TestCall
    {
        $model = $this->factory
            ->has($class::factory(), $relation)
            ->create();

        $this->assertContainsOnlyInstancesOf($class, $model->getAttribute($relation));
        $this->assertCount(1, $model->getAttribute($relation));

        return test();
    }

    /**
     * Tests a has one relation.
     *
     * @param class-string $class
     */
    public function toHaveHasOneRelation(string $class, string $relation): HigherOrderTapProxy|TestCall
    {
        $model = $this->factory
            ->has($class::factory(), $relation)
            ->create();

        $this->assertInstanceOf($class, $model->getAttribute($relation));

        return test();
    }

    /**
     * Tests a has many through relation.
     *
     * @param class-string $class
     * @param class-string $through
     */
    public function toHaveHasManyThroughRelation(string $class, string $through, string $relation): HigherOrderTapProxy|TestCall
    {
        $model = $this->factory->create();

        $through::factory()
            ->for($model)
            ->has($class::factory())
            ->create();

        $this->assertContainsOnlyInstancesOf($class, $model->getAttribute($relation));
        $this->assertCount(1, $model->getAttribute($relation));

        return test();
    }
}
