<?php

namespace App\Http\Controllers\Laradash;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use App\Models\Pais;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        $key = $request->buscar;
        $clientes = Cliente::with('pais')
            ->when($key, fn($q) => $q->filtro($key))
            ->orderBy('id_cliente')
            ->paginate(10)
            ->appends(['buscar' => $key]);

        $paises = Pais::orderBy('nombre')->get(['id_pais', 'nombre']);

        return Inertia::render('Otros/Clientes', [
            'clientes' => $clientes,
            'paises'   => $paises,
            'filtro'   => $key,
        ]);
    }

    public function store(ClienteRequest $request)
    {
        Cliente::create($request->validated());
        return redirect()->back()->with('success', 'Cliente creado con éxito');
    }

    public function update(ClienteRequest $request, $cliente)
    {
        Cliente::findOrFail($cliente)->update($request->validated());
        return redirect()->back()->with('success', 'Cliente actualizado con éxito');
    }

    public function destroy($cliente)
    {
        Cliente::findOrFail($cliente)->delete();
        return redirect()->back()->with('success', 'Cliente eliminado con éxito');
    }
}
