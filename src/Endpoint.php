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

    public function toHaveStoreEndpoint()
    {
        $modelAttributes = $this->factory->make()->toArray();

        $response = $this->postJson($this->endpoint, $modelAttributes)
            ->assertCreated()
            ->assertJson($modelAttributes);

        $this->assertDatabaseCount($this->class, 1);

        return $response;
    }
}
