<?php

namespace Tests\Feature;

use App\Models\Pais;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SucursalesTest extends TestCase
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
        return Pais::create(['nombre' => 'Nicaragua']);
    }

    private function sucursal(Pais $pais): Sucursal
    {
        return Sucursal::factory()->create(['id_pais' => $pais->id_pais]);
    }

    // --- Sucursales ---

    public function test_index_muestra_sucursales_y_vendedores()
    {
        $pais     = $this->pais();
        $sucursal = $this->sucursal($pais);
        Vendedor::factory()->count(2)->create(['id_sucursal' => $sucursal->id_sucursal]);

        $response = $this->actingAs($this->usuario())->get(route('sucursales.index'));

        $response->assertOk()
                 ->assertInertia(fn($page) => $page
                     ->component('Otros/Sucursales')
                     ->has('sucursales.data', 1)
                     ->has('vendedores.data', 2)
                     ->has('paises')
                     ->has('sucursalesSelect')
                 );
    }

    public function test_store_sucursal_valida()
    {
        $pais = $this->pais();

        $this->actingAs($this->usuario())->post(route('sucursales.store'), [
            'codigo'  => 'SUC-01',
            'nombre'  => 'Sucursal Central',
            'id_pais' => $pais->id_pais,
        ])->assertRedirect();

        $this->assertDatabaseHas('sig_sucursales', ['codigo' => 'SUC-01']);
    }

    public function test_store_sucursal_falla_sin_codigo()
    {
        $pais = $this->pais();

        $this->actingAs($this->usuario())->post(route('sucursales.store'), [
            'nombre'  => 'Sin Codigo',
            'id_pais' => $pais->id_pais,
        ])->assertSessionHasErrors(['codigo']);
    }

    public function test_update_sucursal()
    {
        $pais     = $this->pais();
        $sucursal = $this->sucursal($pais);

        $this->actingAs($this->usuario())->put(route('sucursales.update', $sucursal->id_sucursal), [
            'codigo'  => $sucursal->codigo,
            'nombre'  => 'Nombre Actualizado',
            'id_pais' => $pais->id_pais,
        ])->assertRedirect();

        $this->assertDatabaseHas('sig_sucursales', ['nombre' => 'Nombre Actualizado']);
    }

    public function test_destroy_sucursal()
    {
        $pais     = $this->pais();
        $sucursal = $this->sucursal($pais);

        $this->actingAs($this->usuario())->delete(route('sucursales.destroy', $sucursal->id_sucursal))
             ->assertRedirect();

        $this->assertDatabaseMissing('sig_sucursales', ['id_sucursal' => $sucursal->id_sucursal]);
    }

    // --- Vendedores ---

    public function test_store_vendedor_valido()
    {
        $pais     = $this->pais();
        $sucursal = $this->sucursal($pais);

        $this->actingAs($this->usuario())->post(route('vendedores.store'), [
            'codigo_vendedor' => 'VEN-001',
            'nombre_vendedor' => 'Juan Pérez',
            'id_sucursal'     => $sucursal->id_sucursal,
        ])->assertRedirect();

        $this->assertDatabaseHas('sig_vendedores', ['codigo_vendedor' => 'VEN-001']);
    }

    public function test_store_vendedor_falla_sin_nombre()
    {
        $pais     = $this->pais();
        $sucursal = $this->sucursal($pais);

        $this->actingAs($this->usuario())->post(route('vendedores.store'), [
            'codigo_vendedor' => 'VEN-002',
            'id_sucursal'     => $sucursal->id_sucursal,
        ])->assertSessionHasErrors(['nombre_vendedor']);
    }

    public function test_store_vendedor_falla_con_sucursal_inexistente()
    {
        $this->actingAs($this->usuario())->post(route('vendedores.store'), [
            'codigo_vendedor' => 'VEN-003',
            'nombre_vendedor' => 'Test',
            'id_sucursal'     => 9999,
        ])->assertSessionHasErrors(['id_sucursal']);
    }

    public function test_update_vendedor()
    {
        $pais     = $this->pais();
        $sucursal = $this->sucursal($pais);
        $vendedor = Vendedor::factory()->create(['id_sucursal' => $sucursal->id_sucursal]);

        $this->actingAs($this->usuario())->put(route('vendedores.update', $vendedor->id_vendedor), [
            'codigo_vendedor' => $vendedor->codigo_vendedor,
            'nombre_vendedor' => 'Nombre Modificado',
            'id_sucursal'     => $sucursal->id_sucursal,
        ])->assertRedirect();

        $this->assertDatabaseHas('sig_vendedores', ['nombre_vendedor' => 'Nombre Modificado']);
    }

    public function test_destroy_vendedor()
    {
        $pais     = $this->pais();
        $sucursal = $this->sucursal($pais);
        $vendedor = Vendedor::factory()->create(['id_sucursal' => $sucursal->id_sucursal]);

        $this->actingAs($this->usuario())->delete(route('vendedores.destroy', $vendedor->id_vendedor))
             ->assertRedirect();

        $this->assertDatabaseMissing('sig_vendedores', ['id_vendedor' => $vendedor->id_vendedor]);
    }

    public function test_index_filtra_sucursales_por_nombre()
    {
        $pais = $this->pais();
        Sucursal::factory()->create(['nombre' => 'Sucursal Norte', 'id_pais' => $pais->id_pais]);
        Sucursal::factory()->create(['nombre' => 'Sucursal Sur',   'id_pais' => $pais->id_pais]);

        $response = $this->actingAs($this->usuario())
                         ->get(route('sucursales.index', ['buscar_sucursal' => 'Norte']));

        $response->assertInertia(fn($page) => $page->has('sucursales.data', 1));
    }
}
