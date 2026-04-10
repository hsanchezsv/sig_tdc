<?php

namespace Tests\Feature;

use App\Models\Pais;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Vendedor;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function usuario(): User
    {
        return User::factory()->create();
    }

    private function pais(): Pais
    {
        return Pais::create(['nombre' => 'El Salvador']);
    }

    private function sucursal(Pais $pais): Sucursal
    {
        return Sucursal::factory()->create(['id_pais' => $pais->id_pais]);
    }

    private function vendedor(Sucursal $sucursal): Vendedor
    {
        return Vendedor::factory()->create(['id_sucursal' => $sucursal->id_sucursal]);
    }

    private function producto(Pais $pais): Producto
    {
        $proveedor = Proveedor::factory()->create(['id_pais' => $pais->id_pais]);
        return Producto::factory()->create([
            'id_pais'      => $pais->id_pais,
            'id_proveedor' => $proveedor->id_proveedor,
            'precio_unidad' => 50.00,
        ]);
    }

    public function test_index_muestra_ventas()
    {
        $pais      = $this->pais();
        $sucursal  = $this->sucursal($pais);
        $vendedor  = $this->vendedor($sucursal);
        $producto  = $this->producto($pais);

        Venta::factory()->count(2)->create([
            'id_sucursal' => $sucursal->id_sucursal,
            'id_vendedor' => $vendedor->id_vendedor,
            'id_producto' => $producto->id_producto,
            'num_factura' => 'FAC-TEST-001',
        ]);

        $response = $this->actingAs($this->usuario())->get(route('ventas.index'));

        $response->assertOk()
                 ->assertInertia(fn($page) => $page
                     ->component('Ventas/Ventas')
                     ->has('ventas')
                 );
    }

    public function test_store_factura_valida()
    {
        $pais     = $this->pais();
        $sucursal = $this->sucursal($pais);
        $vendedor = $this->vendedor($sucursal);
        $producto = $this->producto($pais);

        $this->actingAs($this->usuario())->post(route('ventas.store'), [
            'fecha_compra' => '2026-03-24',
            'id_sucursal'  => $sucursal->id_sucursal,
            'id_vendedor'  => $vendedor->id_vendedor,
            'num_factura'  => 'FAC-20260324-0001',
            'items'        => [
                [
                    'id_producto'  => $producto->id_producto,
                    'cantidad'     => 2,
                    'precio_unidad' => 50.00,
                ],
            ],
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('sig_ventas', [
            'num_factura' => 'FAC-20260324-0001',
            'id_producto' => $producto->id_producto,
            'cantidad'    => 2,
        ]);
    }

    public function test_store_falla_sin_items()
    {
        $pais     = $this->pais();
        $sucursal = $this->sucursal($pais);
        $vendedor = $this->vendedor($sucursal);

        $this->actingAs($this->usuario())->post(route('ventas.store'), [
            'fecha_compra' => '2026-03-24',
            'id_sucursal'  => $sucursal->id_sucursal,
            'id_vendedor'  => $vendedor->id_vendedor,
            'items'        => [],
        ])->assertSessionHasErrors(['items']);
    }

    public function test_store_falla_producto_inexistente()
    {
        $pais     = $this->pais();
        $sucursal = $this->sucursal($pais);
        $vendedor = $this->vendedor($sucursal);

        $this->actingAs($this->usuario())->post(route('ventas.store'), [
            'fecha_compra' => '2026-03-24',
            'id_sucursal'  => $sucursal->id_sucursal,
            'id_vendedor'  => $vendedor->id_vendedor,
            'items'        => [
                [
                    'id_producto'   => 99999,
                    'cantidad'      => 1,
                    'precio_unidad' => 10.00,
                ],
            ],
        ])->assertSessionHasErrors(['items.0.id_producto']);
    }

    public function test_destroy_elimina_factura()
    {
        $pais     = $this->pais();
        $sucursal = $this->sucursal($pais);
        $vendedor = $this->vendedor($sucursal);
        $producto = $this->producto($pais);

        Venta::factory()->count(2)->create([
            'num_factura' => 'FAC-TEST',
            'id_sucursal' => $sucursal->id_sucursal,
            'id_vendedor' => $vendedor->id_vendedor,
            'id_producto' => $producto->id_producto,
        ]);

        $this->actingAs($this->usuario())
             ->delete(route('ventas.destroy', 'FAC-TEST'))
             ->assertSessionHas('success');

        $this->assertEquals(0, Venta::where('num_factura', 'FAC-TEST')->count());
    }
}
