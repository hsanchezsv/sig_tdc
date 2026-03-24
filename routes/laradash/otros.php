<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Laradash;

Route::get('permisos', [Laradash\PermisosController::class, 'index'])->name('permisos.ver');

Route::get('informes', [Laradash\InformesController::class, 'index'])->name('informes.index');
Route::post('informes', [Laradash\InformesController::class, 'view_informe'])->name('informes.view_informe');

// CRUDs con resource routes
Route::resource('clientes',    Laradash\ClientesController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('proveedores', Laradash\ProveedoresController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('productos',   Laradash\ProductosController::class)->only(['index', 'store', 'update', 'destroy']);

// Sucursales y Vendedores (dos entidades en una página)
Route::get('sucursales',         [Laradash\SucursalesController::class, 'index'])->name('sucursales.index');
Route::post('sucursales',        [Laradash\SucursalesController::class, 'storeSucursal'])->name('sucursales.store');
Route::put('sucursales/{id}',    [Laradash\SucursalesController::class, 'updateSucursal'])->name('sucursales.update');
Route::delete('sucursales/{id}', [Laradash\SucursalesController::class, 'destroySucursal'])->name('sucursales.destroy');
Route::post('vendedores',        [Laradash\SucursalesController::class, 'storeVendedor'])->name('vendedores.store');
Route::put('vendedores/{id}',    [Laradash\SucursalesController::class, 'updateVendedor'])->name('vendedores.update');
Route::delete('vendedores/{id}', [Laradash\SucursalesController::class, 'destroyVendedor'])->name('vendedores.destroy');

// Ventas / Facturas
Route::get('ventas',                  [Laradash\VentasController::class, 'index'])->name('ventas.index');
Route::post('ventas',                 [Laradash\VentasController::class, 'store'])->name('ventas.store');
Route::get('ventas/{num_factura}',    [Laradash\VentasController::class, 'show'])->name('ventas.show');
Route::delete('ventas/{num_factura}', [Laradash\VentasController::class, 'destroy'])->name('ventas.destroy');

// Roles
Route::resource('roles', Laradash\RolesController::class)->names('roles')->except(['create', 'edit', 'show']);

// Usuarios
Route::resource('usuarios', Laradash\UsuariosController::class)->names('usuarios')->only(['index', 'store', 'destroy']);

Route::get('usuario/perfil/{id}',      [Laradash\UsuariosController::class, 'miPerfil'])->name('usuario.perfil');
Route::post('usuario/perfil',          [Laradash\UsuariosController::class, 'actualizarPerfil'])->name('usuario.perfil.actualizar');
Route::post('usuario/perfil/foto',     [Laradash\UsuariosController::class, 'eliminarFoto'])->name('usuario.perfil.eliminarFoto');
Route::post('usuario/perfil/roles',    [Laradash\UsuariosController::class, 'actualizarRoles'])->name('usuario.perfil.roles');
Route::post('usuario/perfil/permisos', [Laradash\UsuariosController::class, 'actualizarPermisos'])->name('usuario.perfil.permisos');
Route::post('usuario/perfil/password', [Laradash\UsuariosController::class, 'actualizarPassword'])->name('usuario.perfil.password');
