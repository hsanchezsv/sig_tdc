<?php

namespace App\Http\Controllers\Laradash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ProductosController extends Controller
{
    public function index(){
        $productos = DB::select('SELECT 
                                    p.id_producto,
                                    p.codigo_producto,
                                    p.nombre_producto,
                                    pv.nombre_proveedor,
                                    p.precio_unidad,
                                    pa.nombre pais,
                                    p.fecha_compra,
                                    p.lote_numero
                                FROM
                                    sig_productos p
                                    INNER JOIN sig_proveedores pv ON pv.id_proveedor = p.id_proveedor
                                    INNER JOIN sig_paises pa ON pa.id_pais = p.id_pais');
        return Inertia::render('Otros/Productos', compact('productos'));
    }
}
