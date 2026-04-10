<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Pais;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition()
    {
        return [
            'nombre_cliente'  => $this->faker->company(),
            'id_pais'         => Pais::factory(),
            'nit'             => $this->faker->numerify('######-#'),
            'direccion'       => $this->faker->address(),
            'contacto_nombre' => $this->faker->name(),
            'telefono'        => $this->faker->phoneNumber(),
            'fecha_ingreso'   => $this->faker->date(),
        ];
    }
}
