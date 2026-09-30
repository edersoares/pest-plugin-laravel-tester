<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester\Tests\Fixtures;

use Dex\Pest\Plugin\Laravel\Tester\Tester;
use Dex\Pest\Plugin\Laravel\Tester\Tests\TestCase;

/**
 * Applies the Tester trait to a concrete test case so PHPStan analyses the traits.
 */
final class TesterTestCase extends TestCase
{
    use Tester;
}
