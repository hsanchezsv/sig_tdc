<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'sig_proveedores';
    protected $primaryKey = 'id_proveedor';
    public $timestamps = false;

    protected $fillable = [
        'nombre_proveedor',
        'id_pais',
        'direccion',
        'telefono',
        'nombre_contacto',
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id_pais');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_proveedor', 'id_proveedor');
    }

    public function scopeFiltro($query, $key)
    {
        return $query->where('nombre_proveedor', 'LIKE', "%{$key}%")
                     ->orWhere('nombre_contacto', 'LIKE', "%{$key}%");
    }
}
