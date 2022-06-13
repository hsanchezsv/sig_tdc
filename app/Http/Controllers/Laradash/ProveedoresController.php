<?php

namespace App\Http\Controllers\Laradash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ProveedoresController extends Controller
{
    public function index(){
        $proveedores = DB::select('SELECT p.id_proveedor,
                                        p.nombre_proveedor,
                                        pa.nombre pais,
                                        p.direccion,
                                        p.telefono,
                                        p.nombre_contacto
                                    FROM sig_proveedores p
                                    INNER JOIN sig_paises pa ON pa.id_pais = p.id_pais
                                ORDER BY id_proveedor ASC');
        return Inertia::render('Otros/Proveedores', compact('proveedores'));
    }
}
