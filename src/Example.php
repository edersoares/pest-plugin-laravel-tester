<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
trait Example
{
    /**
     * Example description.
     */
    public function example(string $name): TestCase
    {
        expect($name)->toBeString();

        return $this;
    }
}
