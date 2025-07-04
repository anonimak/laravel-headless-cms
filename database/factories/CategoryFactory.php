<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(),
            'parent_id' => null, // or $this->faker->numberBetween(1, 10) for a random parent ID
            'created_by' => 1, // assuming user ID 1 exists
            'updated_by' => 1, // assuming user ID 1 exists
            'deleted_by' => null, // null for not deleted
            'uuid' => $this->faker->uuid(),
        ];
    }
}
