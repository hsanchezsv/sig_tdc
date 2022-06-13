<?php

namespace App\Http\Controllers\Laradash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ClientesController extends Controller
{
    public function index(){
        $clientes = DB::select('SELECT 
                                    cl.id_cliente,
                                    cl.nombre_cliente,
                                    p.nombre pais,
                                    cl.nit,
                                    cl.direccion,
                                    cl.contacto_nombre,
                                    cl.telefono,
                                    cl.fecha_ingreso
                                FROM
                                    sig_clientes cl
                                    INNER JOIN sig_paises p ON cl.id_pais = p.id_pais');
        return Inertia::render('Otros/Clientes', compact('clientes'));
    }
}
