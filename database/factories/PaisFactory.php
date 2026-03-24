<?php

namespace Database\Factories;

use App\Models\Pais;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaisFactory extends Factory
{
    protected $model = Pais::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->unique()->country(),
        ];
    }
}
