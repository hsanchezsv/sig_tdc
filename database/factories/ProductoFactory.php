<?php

namespace Database\Factories;

use App\Models\Pais;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition()
    {
        return [
            'codigo_producto' => $this->faker->bothify('PROD-####'),
            'nombre_producto' => $this->faker->words(3, true),
            'id_proveedor'    => Proveedor::factory(),
            'precio_unidad'   => $this->faker->randomFloat(2, 1, 999),
            'id_pais'         => Pais::factory(),
            'fecha_compra'    => $this->faker->date(),
            'lote_numero'     => $this->faker->bothify('LOT-####'),
        ];
    }
}
