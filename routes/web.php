<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('demo', function() {
    return Inertia::render('Demo');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    $totalVentas     = \Illuminate\Support\Facades\DB::table('sig_ventas')
                        ->whereYear('fecha_compra', now()->year)
                        ->sum('monto_facturado');
    $totalVentasAnterior = \Illuminate\Support\Facades\DB::table('sig_ventas')
                        ->whereYear('fecha_compra', now()->subYear()->year)
                        ->sum('monto_facturado');
    $totalClientes   = \App\Models\Cliente::count();
    $totalVendedores = \App\Models\Vendedor::count();
    $totalProductos  = \App\Models\Producto::count();
    $totalSucursales = \App\Models\Sucursal::count();
    $totalProveedores= \App\Models\Proveedor::count();

    return Inertia::render('Dashboard', compact(
        'totalVentas',
        'totalVentasAnterior',
        'totalClientes',
        'totalVendedores',
        'totalProductos',
        'totalSucursales',
        'totalProveedores'
    ));
})->name('dashboard');
