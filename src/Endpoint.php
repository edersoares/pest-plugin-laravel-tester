<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

trait Endpoint
{
    private string $endpoint;

    public function endpoint(string $endpoint): static
    {
        $this->endpoint = $endpoint;

        return $this;
    }
}
