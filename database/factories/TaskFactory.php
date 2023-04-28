<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, 10),
            'description' => fake()->paragraph(),
            'category_id' => Category::factory(),
            'status_id' => Status::factory(),
            'due_at' => fake()->dateTimeBetween('-2 week', '+3 week'),
            'duration' => rand(15, 240),
            'url' => fake()->url()
        ];
    }
}
