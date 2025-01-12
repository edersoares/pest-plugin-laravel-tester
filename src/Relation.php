<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Pest\PendingCalls\TestCall;
use Pest\Support\HigherOrderTapProxy;

trait Relation
{
    use Eloquent;

    public function toHaveBelongsToRelation(string $class, string $relation): HigherOrderTapProxy|TestCall
    {
        $model = $this->factory->create();

        $this->assertInstanceOf($class, $model->getAttribute($relation));
        $this->assertDatabaseCount($model->getTable(), 1);
        $this->assertDatabaseCount($class, 1);

        return test();
    }

    public function toHaveHasManyRelation(string $class, string $relation): HigherOrderTapProxy|TestCall
    {
        $model = $this->factory
            ->has($class::factory(), $relation)
            ->create();

        $this->assertContainsOnlyInstancesOf($class, $model->getAttribute($relation));
        $this->assertCount(1, $model->getAttribute($relation));

        return test();
    }

    public function toHaveHasOneRelation(string $class, string $relation): HigherOrderTapProxy|TestCall
    {
        $model = $this->factory
            ->has($class::factory(), $relation)
            ->create();

        $this->assertInstanceOf($class, $model->getAttribute($relation));

        return test();
    }
}
