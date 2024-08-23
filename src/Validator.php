<?php

declare(strict_types=1);

namespace Dex\Pest\Plugin\Laravel\Tester;

use Illuminate\Support\Str;

trait Validator
{
    public function toValidateRequired(string $attribute)
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

        return test();
    }

    public function toValidateMin(string $attribute, int $min)
    {
        $modelAttributes = $this->factory->make()->toArray();

        $modelAttributes[$attribute] = substr($modelAttributes[$attribute], 0, $min - 1);

        $this->postJson($this->endpoint, $modelAttributes)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        $modelCreated = $this->factory->create();

        $newModel = $this->factory->make()->toArray();

        $newModel[$attribute] = substr($attribute, 0, $min - 1);

        $this->putJson("$this->endpoint/{$modelCreated->getKey()}", $newModel)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor($attribute);

        return test();
    }

    public function toValidateMax(string $attribute, int $max)
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

        return test();
    }

    public function toValidateSize(string $attribute, int $size)
    {
        $modelAttributes = $this->factory->make()->toArray();

        $modelAttributes[$attribute] = substr($modelAttributes[$attribute], 0, $size - 1);

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

        return test();
    }
}
