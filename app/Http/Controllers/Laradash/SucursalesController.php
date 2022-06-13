<?php

namespace App\Http\Controllers\Laradash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class SucursalesController extends Controller
{
    public function index(){
        $sucursales = DB::select('SELECT s.id_sucursal, s.codigo, s.nombre as nombre_sucursal, p.nombre as pais
                                    FROM sig_sucursales s
                                    INNER JOIN sig_paises p ON s.id_pais = p.id_pais
                                    ORDER BY id_sucursal ASC');

        $vendedores = DB::select('SELECT 
                                        v.id_vendedor,
                                        v.codigo_vendedor,
                                        v.nombre_vendedor,
                                        v.fecha_ingreso,
                                        v.numero_documento,
                                        p.nombre AS pais,
                                        s.nombre AS sucursal
                                    FROM
                                        sig_vendedores v
                                            INNER JOIN
                                        sig_sucursales s ON s.id_sucursal = v.id_sucursal
                                            INNER JOIN
                                        sig_paises p ON s.id_pais = p.id_pais
                                    ORDER BY p.id_pais ASC');
        return Inertia::render('Otros/Sucursales', [
                    'sucursales' => $sucursales, 
                    'vendedores' => $vendedores,
                ]);
    }
}
