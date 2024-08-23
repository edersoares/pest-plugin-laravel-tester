<?php

declare(strict_types=1);

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Post;
use Workbench\App\Models\User;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => fn () => User::factory()->create(),
            'title' => $this->faker->words(5, true),
            'content' => $this->faker->sentence(5),
        ];
    }
}
