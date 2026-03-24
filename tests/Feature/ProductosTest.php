<?php

namespace Tests\Feature;

use App\Models\Pais;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductosTest extends TestCase
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
        return Pais::create(['nombre' => 'Honduras']);
    }

    private function proveedor(Pais $pais): Proveedor
    {
        return Proveedor::factory()->create(['id_pais' => $pais->id_pais]);
    }

    public function test_index_muestra_productos_paginados()
    {
        $pais      = $this->pais();
        $proveedor = $this->proveedor($pais);
        Producto::factory()->count(2)->create([
            'id_pais'      => $pais->id_pais,
            'id_proveedor' => $proveedor->id_proveedor,
        ]);

        $response = $this->actingAs($this->usuario())->get(route('productos.index'));

        $response->assertOk()
                 ->assertInertia(fn($page) => $page
                     ->component('Otros/Productos')
                     ->has('productos.data', 2)
                     ->has('paises')
                     ->has('proveedores')
                 );
    }

    public function test_store_crea_producto_valido()
    {
        $pais      = $this->pais();
        $proveedor = $this->proveedor($pais);

        $this->actingAs($this->usuario())->post(route('productos.store'), [
            'codigo_producto' => 'PROD-001',
            'nombre_producto' => 'Producto de Prueba',
            'id_proveedor'    => $proveedor->id_proveedor,
            'id_pais'         => $pais->id_pais,
            'precio_unidad'   => 25.50,
        ])->assertRedirect();

        $this->assertDatabaseHas('sig_productos', ['codigo_producto' => 'PROD-001']);
    }

    public function test_store_falla_sin_codigo()
    {
        $pais      = $this->pais();
        $proveedor = $this->proveedor($pais);

        $this->actingAs($this->usuario())->post(route('productos.store'), [
            'nombre_producto' => 'Producto Sin Codigo',
            'id_proveedor'    => $proveedor->id_proveedor,
            'id_pais'         => $pais->id_pais,
        ])->assertSessionHasErrors(['codigo_producto']);
    }

    public function test_store_falla_con_proveedor_inexistente()
    {
        $pais = $this->pais();

        $this->actingAs($this->usuario())->post(route('productos.store'), [
            'codigo_producto' => 'PROD-X',
            'nombre_producto' => 'Producto Test',
            'id_proveedor'    => 9999,
            'id_pais'         => $pais->id_pais,
        ])->assertSessionHasErrors(['id_proveedor']);
    }

    public function test_update_modifica_producto()
    {
        $pais      = $this->pais();
        $proveedor = $this->proveedor($pais);
        $producto  = Producto::factory()->create([
            'id_pais'      => $pais->id_pais,
            'id_proveedor' => $proveedor->id_proveedor,
        ]);

        $this->actingAs($this->usuario())->put(route('productos.update', $producto->id_producto), [
            'codigo_producto' => $producto->codigo_producto,
            'nombre_producto' => 'Nombre Modificado',
            'id_proveedor'    => $proveedor->id_proveedor,
            'id_pais'         => $pais->id_pais,
        ])->assertRedirect();

        $this->assertDatabaseHas('sig_productos', ['nombre_producto' => 'Nombre Modificado']);
    }

    public function test_destroy_elimina_producto()
    {
        $pais      = $this->pais();
        $proveedor = $this->proveedor($pais);
        $producto  = Producto::factory()->create([
            'id_pais'      => $pais->id_pais,
            'id_proveedor' => $proveedor->id_proveedor,
        ]);

        $this->actingAs($this->usuario())->delete(route('productos.destroy', $producto->id_producto))
             ->assertRedirect();

        $this->assertDatabaseMissing('sig_productos', ['id_producto' => $producto->id_producto]);
    }

    public function test_index_filtra_por_nombre()
    {
        $pais      = $this->pais();
        $proveedor = $this->proveedor($pais);
        Producto::factory()->create([
            'nombre_producto' => 'Filtro de Aceite',
            'id_pais'         => $pais->id_pais,
            'id_proveedor'    => $proveedor->id_proveedor,
        ]);
        Producto::factory()->create([
            'nombre_producto' => 'Tuerca Hexagonal',
            'id_pais'         => $pais->id_pais,
            'id_proveedor'    => $proveedor->id_proveedor,
        ]);

        $response = $this->actingAs($this->usuario())
                         ->get(route('productos.index', ['buscar' => 'Aceite']));

        $response->assertInertia(fn($page) => $page->has('productos.data', 1));
    }
}
