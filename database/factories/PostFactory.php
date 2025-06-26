<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'slug' => $this->faker->unique()->slug(),
            'excerpt' => $this->faker->text(100),
            'image' => $this->faker->imageUrl(640, 480, 'posts'),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'created_by' => 1, // assuming user ID 1 exists
            'updated_by' => 1, // assuming user ID 1 exists
            'deleted_by' => null, // null for not deleted
            'uuid' => $this->faker->uuid(),
        ];
    }
}
