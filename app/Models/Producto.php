<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'sig_productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
        'codigo_producto',
        'nombre_producto',
        'id_proveedor',
        'precio_unidad',
        'id_pais',
        'fecha_compra',
        'lote_numero',
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id_pais');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function scopeFiltro($query, $key)
    {
        return $query->where('nombre_producto', 'LIKE', "%{$key}%")
                     ->orWhere('codigo_producto', 'LIKE', "%{$key}%");
    }
}
