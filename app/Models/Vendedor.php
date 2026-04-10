<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendedor extends Model
{
    use HasFactory;

    protected $table = 'sig_vendedores';
    protected $primaryKey = 'id_vendedor';
    public $timestamps = false;

    protected $fillable = [
        'codigo_vendedor',
        'nombre_vendedor',
        'fecha_ingreso',
        'numero_documento',
        'id_sucursal',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }

    public function scopeFiltro($query, $key)
    {
        return $query->where('nombre_vendedor', 'LIKE', "%{$key}%")
                     ->orWhere('codigo_vendedor', 'LIKE', "%{$key}%");
    }
}
