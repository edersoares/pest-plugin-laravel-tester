<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

trait Relation
{
    use Eloquent;

    public function toHaveBelongsToRelation(string $class, string $relation)
    {
        $model = $this->factory->create();

        $this->assertInstanceOf($class, $model->getAttribute($relation));
        $this->assertDatabaseCount($model->getTable(), 1);
        $this->assertDatabaseCount($class, 1);

        return test();
    }
}
