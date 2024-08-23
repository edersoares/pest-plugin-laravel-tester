<?php

use function Dex\Pest\Plugin\Laravel\Tester\example;

it('may be accessed on the `$this` closure', function () {
    $this->example('foo');
});

it('may be accessed as function', function () {
    example('foo');
});
