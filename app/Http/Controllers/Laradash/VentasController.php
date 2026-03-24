<?php

namespace App\Http\Controllers\Laradash;

use App\Http\Controllers\Controller;
use App\Http\Requests\VentaRequest;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Vendedor;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VentasController extends Controller
{
    public function index(Request $request)
    {
        $key = $request->buscar;

        $ventasQuery = DB::table('sig_ventas')
            ->select(
                'num_factura',
                'fecha_compra',
                'id_sucursal',
                'id_vendedor',
                DB::raw('SUM(monto_facturado) as total'),
                DB::raw('SUM(cantidad) as total_items'),
                DB::raw('COUNT(*) as num_productos')
            )
            ->groupBy('num_factura', 'fecha_compra', 'id_sucursal', 'id_vendedor')
            ->when($key, fn($q) => $q->where('num_factura', 'LIKE', "%{$key}%"))
            ->orderByDesc('fecha_compra');

        $ventas = $ventasQuery->paginate(10)->appends(['buscar' => $key]);

        // Cargar relaciones manualmente para la colección paginada
        $idsSucursal = $ventas->pluck('id_sucursal')->unique()->filter()->values();
        $idsVendedor = $ventas->pluck('id_vendedor')->unique()->filter()->values();

        $sucursalesMap = Sucursal::whereIn('id_sucursal', $idsSucursal)
            ->get(['id_sucursal', 'nombre'])
            ->keyBy('id_sucursal');

        $vendedoresMap = Vendedor::whereIn('id_vendedor', $idsVendedor)
            ->get(['id_vendedor', 'nombre_vendedor'])
            ->keyBy('id_vendedor');

        $ventas->getCollection()->transform(function ($row) use ($sucursalesMap, $vendedoresMap) {
            $row->sucursal = $sucursalesMap->get($row->id_sucursal);
            $row->vendedor = $vendedoresMap->get($row->id_vendedor);
            return $row;
        });

        $sucursales = Sucursal::orderBy('nombre')->get(['id_sucursal', 'nombre']);
        $vendedores = Vendedor::orderBy('nombre_vendedor')
            ->get(['id_vendedor', 'nombre_vendedor', 'id_sucursal']);
        $productos  = Producto::orderBy('nombre_producto')
            ->get(['id_producto', 'nombre_producto', 'codigo_producto', 'precio_unidad']);

        return Inertia::render('Ventas/Ventas', [
            'ventas'      => $ventas,
            'sucursales'  => $sucursales,
            'vendedores'  => $vendedores,
            'productos'   => $productos,
            'filtro'      => $key,
        ]);
    }

    public function store(VentaRequest $request)
    {
        $numFactura = $request->num_factura;

        if (empty($numFactura)) {
            $siguienteId = (int) DB::table('sig_ventas')->max('id_venta') + 1;
            $numFactura  = 'FAC-' . date('Ymd') . '-' . str_pad($siguienteId, 4, '0', STR_PAD_LEFT);
        }

        foreach ($request->items as $item) {
            Venta::create([
                'fecha_compra'    => $request->fecha_compra,
                'num_factura'     => $numFactura,
                'id_sucursal'     => $request->id_sucursal,
                'id_vendedor'     => $request->id_vendedor,
                'id_producto'     => $item['id_producto'],
                'cantidad'        => $item['cantidad'],
                'monto_facturado' => $item['precio_unidad'] * $item['cantidad'],
            ]);
        }

        return redirect()->back()->with('success', 'Factura emitida con éxito');
    }

    public function destroy($num_factura)
    {
        Venta::where('num_factura', $num_factura)->delete();
        return redirect()->back()->with('success', 'Factura eliminada con éxito');
    }

    public function show($num_factura)
    {
        $items = Venta::with(['sucursal', 'vendedor', 'producto'])
            ->where('num_factura', $num_factura)
            ->get();

        return response()->json($items);
    }
}
