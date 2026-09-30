<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Str;

/**
 * @mixin TestCase
 */
trait Validator
{
    /**
     * Tests if required rule is set for attribute.
     */
    public function toValidateRequired(string $attribute): static
    {
        $modelAttributes = $this->factory->make()->toArray();

        unset($modelAttributes[$attribute]);

        $this->postJson($this->endpoint, $modelAttributes)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        $modelCreated = $this->factory->create();

        $newModel = $this->factory->make()->toArray();

        unset($newModel[$attribute]);

        $this->putJson("$this->endpoint/{$modelCreated->getKey()}", $newModel)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        return $this;
    }

    /**
     * Tests if min rule is set for attribute.
     */
    public function toValidateMin(string $attribute, int $min): static
    {
        $modelAttributes = $this->factory->make()->toArray();

        $modelAttributes[$attribute] = Str::random($min - 1);

        $this->postJson($this->endpoint, $modelAttributes)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        $modelCreated = $this->factory->create();

        $newModel = $this->factory->make()->toArray();

        $newModel[$attribute] = substr($attribute, 0, $min - 1);

        $this->putJson("$this->endpoint/{$modelCreated->getKey()}", $newModel)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        return $this;
    }

    /**
     * Tests if max rule is set for attribute.
     */
    public function toValidateMax(string $attribute, int $max): static
    {
        $modelAttributes = $this->factory->make()->toArray();

        $modelAttributes[$attribute] = Str::random($max + 1);

        $this->postJson($this->endpoint, $modelAttributes)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        $modelCreated = $this->factory->create();

        $newModel = $this->factory->make()->toArray();

        $newModel[$attribute] = Str::random($max + 1);

        $this->putJson("$this->endpoint/{$modelCreated->getKey()}", $newModel)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        return $this;
    }

    /**
     * Tests if size rule is set for attribute.
     */
    public function toValidateSize(string $attribute, int $size): static
    {
        $modelAttributes = $this->factory->make()->toArray();

        $modelAttributes[$attribute] = substr((string) $modelAttributes[$attribute], 0, $size - 1);

        $this->postJson($this->endpoint, $modelAttributes)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        $modelCreated = $this->factory->create();

        $newModel = $this->factory->make()->toArray();

        $newModel[$attribute] = substr($attribute, 0, $size - 1);

        $this->putJson("$this->endpoint/{$modelCreated->getKey()}", $newModel)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        $modelAttributes = $this->factory->make()->toArray();

        $modelAttributes[$attribute] = Str::random($size + 1);

        $this->postJson($this->endpoint, $modelAttributes)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        $modelCreated = $this->factory->create();

        $newModel = $this->factory->make()->toArray();

        $newModel[$attribute] = Str::random($size + 1);

        $this->putJson("$this->endpoint/{$modelCreated->getKey()}", $newModel)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        return $this;
    }
}
