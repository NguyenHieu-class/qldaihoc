<?php

namespace Database\Factories;

use App\Models\Degree;
use Illuminate\Database\Eloquent\Factories\Factory;

class DegreeFactory extends Factory
{
    protected $model = Degree::class;

    public function definition(): array
    {
        return [
            'name' => 'Dr',
            'coefficient' => 1,
        ];
    }
}
