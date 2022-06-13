<?php

namespace App\Http\Controllers\Laradash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InformesController extends Controller
{
    public function index(){
        return Inertia::render('Otros/InformesGerenciales');
    }

    public function view_informe(Request $request){

        $periodo_inicio = $request->fecha_inicio;
        $periodo_fin = $request->fecha_fin;
        $vendedores = DB::select('SELECT 
                                    SUM(f.monto_facturado) AS venta_vendedor,
                                    f.id_vendedor,
                                    f.id_sucursal,
                                    COUNT(f.fecha_compra) AS num_ventas,
                                    v.codigo_vendedor,
                                    v.nombre_vendedor,
                                    t.nombre AS sucursal,
                                    p.id_pais,
                                    p.nombre AS pais
                                FROM
                                    sig_ventas f
                                        INNER JOIN
                                    sig_vendedores v ON f.id_vendedor = v.id_vendedor
                                        INNER JOIN
                                    sig_sucursales t ON t.id_sucursal = v.id_sucursal
                                        INNER JOIN
                                    sig_paises p ON t.id_pais = p.id_pais
                                WHERE f.fecha_compra BETWEEN "' . $periodo_inicio . '" AND  "'. $periodo_fin . '"
                                GROUP BY v.id_vendedor
                                ORDER BY venta_vendedor DESC');

        $total_facturado = DB::select('SELECT 
                                            SUM(f.monto_facturado) AS total_facturado,
                                            COUNT(f.fecha_compra) AS total_num_ventas,
                                            (SUM(f.monto_facturado)/COUNT(f.fecha_compra)) AS promedio_ventas
                                        FROM
                                            sig_ventas f
                                                INNER JOIN
                                            sig_vendedores v ON f.id_vendedor = v.id_vendedor
                                                INNER JOIN
                                            sig_sucursales t ON t.id_sucursal = v.id_sucursal
                                                INNER JOIN
                                            sig_paises p ON t.id_pais = p.id_pais
                                            WHERE f.fecha_compra BETWEEN "' . $periodo_inicio . '" AND  "'. $periodo_fin . '"
                                        ');
        
        $meses = DB::select('SELECT mes FROM (SELECT 
                                MONTHNAME(f.fecha_compra) AS mes,
                                MONTH(f.fecha_compra) AS id_mes
                            
                            FROM
                                sig_ventas f
                            
                                WHERE f.fecha_compra >= date_sub(curdate(), interval 5 month)
                            GROUP BY id_mes
                            ORDER BY id_mes ASC) meses');

        $ultimos_6_meses = DB::select('SELECT 
                                            SUM(f.monto_facturado) AS facturado,
                                            MONTHNAME(f.fecha_compra) AS mes,
                                            MONTH(f.fecha_compra) AS id_mes
                                        FROM
                                            sig_ventas f
                                                INNER JOIN
                                            sig_vendedores v ON f.id_vendedor = v.id_vendedor
                                                INNER JOIN
                                            sig_sucursales t ON t.id_sucursal = v.id_sucursal
                                                INNER JOIN
                                            sig_paises p ON t.id_pais = p.id_pais
                                            WHERE f.fecha_compra >= date_sub(curdate(), interval 5 month)
                                        GROUP BY id_mes
                                        ORDER BY id_mes ASC');
        
        $ventas_x_sucursal = DB::select('SELECT 
                                            SUM(f.monto_facturado) AS venta_sucursal,
                                            f.id_vendedor,
                                            f.id_sucursal,
                                            COUNT(f.fecha_compra) AS num_ventas,
                                            v.codigo_vendedor,
                                            v.nombre_vendedor,
                                            t.nombre AS sucursal,
                                            p.id_pais,
                                            p.nombre AS pais,
                                            p.iso_code
                                        FROM
                                            sig_ventas f
                                                INNER JOIN
                                            sig_vendedores v ON f.id_vendedor = v.id_vendedor
                                                INNER JOIN
                                            sig_sucursales t ON t.id_sucursal = v.id_sucursal
                                                INNER JOIN
                                            sig_paises p ON t.id_pais = p.id_pais
                                        WHERE f.fecha_compra BETWEEN "' . $periodo_inicio . '" AND  "'. $periodo_fin . '"
                                        GROUP BY v.id_sucursal
                                        ORDER BY venta_sucursal DESC');
        
        switch($request->tipo_reporte){
            case 1:

                return Inertia::render('Informes/InformeVentas', [
                    'vendedores' => $vendedores, 
                    'periodo_inicio' => $periodo_inicio,
                    'periodo_fin' => $periodo_fin,
                    'total_facturado' => $total_facturado,
                    'meses' => $meses,
                    'ultimos_6_meses' => $ultimos_6_meses
                ]);
                break;

            case 2:
                
                return Inertia::render('Informes/InformeSucursal', [
                    'vendedores' => $vendedores, 
                    'periodo_inicio' => $periodo_inicio,
                    'periodo_fin' => $periodo_fin,
                    'total_facturado' => $total_facturado,
                    'meses' => $meses,
                    'ultimos_6_meses' => $ultimos_6_meses,
                    'ventas_x_sucursal' => $ventas_x_sucursal
                ]);
                break;
            default:
                
        }
        
    }
}
