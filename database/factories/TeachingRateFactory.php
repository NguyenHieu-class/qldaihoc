<?php

namespace Database\Factories;

use App\Models\TeachingRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeachingRateFactory extends Factory
{
    protected $model = TeachingRate::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 50, 200),
        ];
    }
}
