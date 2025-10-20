<?php

namespace Database\Factories;

use App\Models\SystemLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SystemLog>
 **/
class SystemLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'message' => fake()->sentence(),
        ];
    }
}
