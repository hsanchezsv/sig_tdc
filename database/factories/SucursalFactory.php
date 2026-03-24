<?php

namespace Database\Factories;

use App\Models\Pais;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

class SucursalFactory extends Factory
{
    protected $model = Sucursal::class;

    public function definition()
    {
        return [
            'codigo'  => $this->faker->bothify('SUC-##'),
            'nombre'  => $this->faker->city() . ' - ' . $this->faker->companySuffix(),
            'id_pais' => Pais::factory(),
        ];
    }
}
