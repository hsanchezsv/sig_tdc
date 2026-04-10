<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pais extends Model
{
    use HasFactory;
    protected $table = 'sig_paises';
    protected $primaryKey = 'id_pais';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_pais', 'id_pais');
    }

    public function proveedores()
    {
        return $this->hasMany(Proveedor::class, 'id_pais', 'id_pais');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_pais', 'id_pais');
    }

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'id_pais', 'id_pais');
    }
}
