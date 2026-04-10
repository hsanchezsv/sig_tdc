<?php

namespace App\Http\Controllers\Laradash;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProveedorRequest;
use App\Models\Pais;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProveedoresController extends Controller
{
    public function index(Request $request)
    {
        $key = $request->buscar;
        $proveedores = Proveedor::with('pais')
            ->when($key, fn($q) => $q->filtro($key))
            ->orderBy('id_proveedor')
            ->paginate(10)
            ->appends(['buscar' => $key]);

        $paises = Pais::orderBy('nombre')->get(['id_pais', 'nombre']);

        return Inertia::render('Otros/Proveedores', [
            'proveedores' => $proveedores,
            'paises'      => $paises,
            'filtro'      => $key,
        ]);
    }

    public function store(ProveedorRequest $request)
    {
        Proveedor::create($request->validated());
        return redirect()->back()->with('success', 'Proveedor creado con éxito');
    }

    public function update(ProveedorRequest $request, $proveedor)
    {
        Proveedor::findOrFail($proveedor)->update($request->validated());
        return redirect()->back()->with('success', 'Proveedor actualizado con éxito');
    }

    public function destroy($proveedor)
    {
        Proveedor::findOrFail($proveedor)->delete();
        return redirect()->back()->with('success', 'Proveedor eliminado con éxito');
    }
}
