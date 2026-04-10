<?php

namespace Database\Factories;

use App\Models\Sucursal;
use App\Models\Vendedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendedorFactory extends Factory
{
    protected $model = Vendedor::class;

    public function definition()
    {
        return [
            'codigo_vendedor'  => $this->faker->bothify('VEN-####'),
            'nombre_vendedor'  => $this->faker->name(),
            'fecha_ingreso'    => $this->faker->date(),
            'numero_documento' => $this->faker->numerify('########'),
            'id_sucursal'      => Sucursal::factory(),
        ];
    }
}
