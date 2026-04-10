<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Pais;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientesTest extends TestCase
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
        return Pais::create(['nombre' => 'Guatemala']);
    }

    public function test_index_muestra_clientes_paginados()
    {
        $pais = $this->pais();
        Cliente::factory()->count(3)->create(['id_pais' => $pais->id_pais]);

        $response = $this->actingAs($this->usuario())->get(route('clientes.index'));

        $response->assertOk()
                 ->assertInertia(fn($page) => $page
                     ->component('Otros/Clientes')
                     ->has('clientes.data', 3)
                     ->has('paises')
                 );
    }

    public function test_store_crea_cliente_valido()
    {
        $pais = $this->pais();

        $this->actingAs($this->usuario())->post(route('clientes.store'), [
            'nombre_cliente' => 'Empresa Test SA',
            'id_pais'        => $pais->id_pais,
            'nit'            => '123456-7',
        ])->assertRedirect();

        $this->assertDatabaseHas('sig_clientes', ['nombre_cliente' => 'Empresa Test SA']);
    }

    public function test_store_falla_sin_nombre()
    {
        $pais = $this->pais();

        $this->actingAs($this->usuario())->post(route('clientes.store'), [
            'id_pais' => $pais->id_pais,
        ])->assertSessionHasErrors(['nombre_cliente']);
    }

    public function test_store_falla_con_pais_inexistente()
    {
        $this->actingAs($this->usuario())->post(route('clientes.store'), [
            'nombre_cliente' => 'Empresa Test',
            'id_pais'        => 9999,
        ])->assertSessionHasErrors(['id_pais']);
    }

    public function test_update_modifica_cliente()
    {
        $pais    = $this->pais();
        $cliente = Cliente::factory()->create(['id_pais' => $pais->id_pais]);

        $this->actingAs($this->usuario())->put(route('clientes.update', $cliente->id_cliente), [
            'nombre_cliente' => 'Nombre Modificado',
            'id_pais'        => $pais->id_pais,
        ])->assertRedirect();

        $this->assertDatabaseHas('sig_clientes', ['nombre_cliente' => 'Nombre Modificado']);
    }

    public function test_destroy_elimina_cliente()
    {
        $pais    = $this->pais();
        $cliente = Cliente::factory()->create(['id_pais' => $pais->id_pais]);

        $this->actingAs($this->usuario())->delete(route('clientes.destroy', $cliente->id_cliente))
             ->assertRedirect();

        $this->assertDatabaseMissing('sig_clientes', ['id_cliente' => $cliente->id_cliente]);
    }

    public function test_index_filtra_por_nombre()
    {
        $pais = $this->pais();
        Cliente::factory()->create(['nombre_cliente' => 'Empresa XYZ', 'id_pais' => $pais->id_pais]);
        Cliente::factory()->create(['nombre_cliente' => 'Comercial ABC', 'id_pais' => $pais->id_pais]);

        $response = $this->actingAs($this->usuario())
                         ->get(route('clientes.index', ['buscar' => 'XYZ']));

        $response->assertInertia(fn($page) => $page->has('clientes.data', 1));
    }
}
