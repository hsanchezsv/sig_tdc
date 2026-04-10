<?php

namespace App\Http\Controllers\Laradash;

use App\Http\Controllers\Controller;
use App\Http\Requests\SucursalRequest;
use App\Http\Requests\VendedorRequest;
use App\Models\Pais;
use App\Models\Sucursal;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SucursalesController extends Controller
{
    public function index(Request $request)
    {
        $keySuc = $request->buscar_sucursal;
        $keyVen = $request->buscar_vendedor;

        $sucursales = Sucursal::with('pais')
            ->when($keySuc, fn($q) => $q->filtro($keySuc))
            ->orderBy('id_sucursal')
            ->paginate(10, ['*'], 'pagina_suc')
            ->appends(['buscar_sucursal' => $keySuc, 'buscar_vendedor' => $keyVen]);

        $vendedores = Vendedor::with('sucursal.pais')
            ->when($keyVen, fn($q) => $q->filtro($keyVen))
            ->orderBy('id_vendedor')
            ->paginate(10, ['*'], 'pagina_ven')
            ->appends(['buscar_sucursal' => $keySuc, 'buscar_vendedor' => $keyVen]);

        $sucursalesSelect = Sucursal::orderBy('nombre')->get(['id_sucursal', 'nombre']);
        $paises = Pais::orderBy('nombre')->get(['id_pais', 'nombre']);

        return Inertia::render('Otros/Sucursales', [
            'sucursales'       => $sucursales,
            'vendedores'       => $vendedores,
            'sucursalesSelect' => $sucursalesSelect,
            'paises'           => $paises,
            'filtroSucursal'   => $keySuc,
            'filtroVendedor'   => $keyVen,
        ]);
    }

    // --- Sucursales CRUD ---

    public function storeSucursal(SucursalRequest $request)
    {
        Sucursal::create($request->validated());
        return redirect()->back()->with('success', 'Sucursal creada con éxito');
    }

    public function updateSucursal(SucursalRequest $request, $id)
    {
        Sucursal::findOrFail($id)->update($request->validated());
        return redirect()->back()->with('success', 'Sucursal actualizada con éxito');
    }

    public function destroySucursal($id)
    {
        Sucursal::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Sucursal eliminada con éxito');
    }

    // --- Vendedores CRUD ---

    public function storeVendedor(VendedorRequest $request)
    {
        Vendedor::create($request->validated());
        return redirect()->back()->with('success', 'Vendedor creado con éxito');
    }

    public function updateVendedor(VendedorRequest $request, $id)
    {
        Vendedor::findOrFail($id)->update($request->validated());
        return redirect()->back()->with('success', 'Vendedor actualizado con éxito');
    }

    public function destroyVendedor($id)
    {
        Vendedor::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Vendedor eliminado con éxito');
    }
}
