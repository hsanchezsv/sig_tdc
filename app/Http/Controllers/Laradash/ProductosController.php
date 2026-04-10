<?php

namespace App\Http\Controllers\Laradash;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductoRequest;
use App\Models\Pais;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductosController extends Controller
{
    public function index(Request $request)
    {
        $key = $request->buscar;
        $productos = Producto::with(['pais', 'proveedor'])
            ->when($key, fn($q) => $q->filtro($key))
            ->orderBy('id_producto')
            ->paginate(10)
            ->appends(['buscar' => $key]);

        $paises      = Pais::orderBy('nombre')->get(['id_pais', 'nombre']);
        $proveedores = Proveedor::orderBy('nombre_proveedor')->get(['id_proveedor', 'nombre_proveedor']);

        return Inertia::render('Otros/Productos', [
            'productos'   => $productos,
            'paises'      => $paises,
            'proveedores' => $proveedores,
            'filtro'      => $key,
        ]);
    }

    public function store(ProductoRequest $request)
    {
        Producto::create($request->validated());
        return redirect()->back()->with('success', 'Producto creado con éxito');
    }

    public function update(ProductoRequest $request, $producto)
    {
        Producto::findOrFail($producto)->update($request->validated());
        return redirect()->back()->with('success', 'Producto actualizado con éxito');
    }

    public function destroy($producto)
    {
        Producto::findOrFail($producto)->delete();
        return redirect()->back()->with('success', 'Producto eliminado con éxito');
    }
}
