<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\Vendedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VentaFactory extends Factory
{
    protected $model = Venta::class;

    public function definition()
    {
        $cantidad      = $this->faker->numberBetween(1, 10);
        $precioUnidad  = $this->faker->randomFloat(2, 10, 500);

        return [
            'fecha_compra'    => $this->faker->date(),
            'num_factura'     => 'FAC-' . $this->faker->unique()->numerify('########'),
            'id_producto'     => Producto::factory(),
            'id_vendedor'     => Vendedor::factory(),
            'id_sucursal'     => Sucursal::factory(),
            'monto_facturado' => $precioUnidad * $cantidad,
            'cantidad'        => $cantidad,
            'id_promocion'    => null,
        ];
    }
}
