<?php

namespace Database\Factories;

use App\Models\Pais;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    protected $model = Proveedor::class;

    public function definition()
    {
        return [
            'nombre_proveedor' => $this->faker->company(),
            'id_pais'          => Pais::factory(),
            'direccion'        => $this->faker->address(),
            'telefono'         => $this->faker->phoneNumber(),
            'nombre_contacto'  => $this->faker->name(),
        ];
    }
}
