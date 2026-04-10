<?php

namespace Tests\Feature;

use App\Models\Pais;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProveedoresTest extends TestCase
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

    public function test_index_muestra_proveedores_paginados()
    {
        $pais = $this->pais();
        Proveedor::factory()->count(2)->create(['id_pais' => $pais->id_pais]);

        $response = $this->actingAs($this->usuario())->get(route('proveedores.index'));

        $response->assertOk()
                 ->assertInertia(fn($page) => $page
                     ->component('Otros/Proveedores')
                     ->has('proveedores.data', 2)
                     ->has('paises')
                 );
    }

    public function test_store_crea_proveedor_valido()
    {
        $pais = $this->pais();

        $this->actingAs($this->usuario())->post(route('proveedores.store'), [
            'nombre_proveedor' => 'Distribuidora Central',
            'id_pais'          => $pais->id_pais,
        ])->assertRedirect();

        $this->assertDatabaseHas('sig_proveedores', ['nombre_proveedor' => 'Distribuidora Central']);
    }

    public function test_store_falla_sin_nombre()
    {
        $pais = $this->pais();

        $this->actingAs($this->usuario())->post(route('proveedores.store'), [
            'id_pais' => $pais->id_pais,
        ])->assertSessionHasErrors(['nombre_proveedor']);
    }

    public function test_store_falla_con_pais_inexistente()
    {
        $this->actingAs($this->usuario())->post(route('proveedores.store'), [
            'nombre_proveedor' => 'Proveedor Test',
            'id_pais'          => 9999,
        ])->assertSessionHasErrors(['id_pais']);
    }

    public function test_update_modifica_proveedor()
    {
        $pais      = $this->pais();
        $proveedor = Proveedor::factory()->create(['id_pais' => $pais->id_pais]);

        $this->actingAs($this->usuario())->put(route('proveedores.update', $proveedor->id_proveedor), [
            'nombre_proveedor' => 'Nombre Actualizado',
            'id_pais'          => $pais->id_pais,
        ])->assertRedirect();

        $this->assertDatabaseHas('sig_proveedores', ['nombre_proveedor' => 'Nombre Actualizado']);
    }

    public function test_destroy_elimina_proveedor()
    {
        $pais      = $this->pais();
        $proveedor = Proveedor::factory()->create(['id_pais' => $pais->id_pais]);

        $this->actingAs($this->usuario())->delete(route('proveedores.destroy', $proveedor->id_proveedor))
             ->assertRedirect();

        $this->assertDatabaseMissing('sig_proveedores', ['id_proveedor' => $proveedor->id_proveedor]);
    }

    public function test_index_filtra_por_nombre()
    {
        $pais = $this->pais();
        Proveedor::factory()->create(['nombre_proveedor' => 'Distribuidora Norte', 'id_pais' => $pais->id_pais]);
        Proveedor::factory()->create(['nombre_proveedor' => 'Importadora Sur',     'id_pais' => $pais->id_pais]);

        $response = $this->actingAs($this->usuario())
                         ->get(route('proveedores.index', ['buscar' => 'Norte']));

        $response->assertInertia(fn($page) => $page->has('proveedores.data', 1));
    }
}
