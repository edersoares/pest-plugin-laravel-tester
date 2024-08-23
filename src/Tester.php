<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Illuminate\Foundation\Testing\TestCase;

/**
 * @mixin TestCase
 */
trait Tester
{
    use Eloquent;
    use Endpoint;
    use Relation;
    use Validator;
}
