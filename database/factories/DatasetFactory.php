<?php

namespace Database\Factories;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dataset>
 */
class DatasetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->word().'.csv',
            'file_path' => 'datasets/'.fake()->word().'.csv',
            'schema_json' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'int'],
                    ['name' => 'value', 'type' => 'float'],
                ],
            ],
            'row_count' => fake()->numberBetween(10, 10000),
        ];
    }
}
