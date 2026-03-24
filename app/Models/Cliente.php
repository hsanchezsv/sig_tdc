<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'sig_clientes';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;

    protected $fillable = [
        'nombre_cliente',
        'id_pais',
        'nit',
        'direccion',
        'contacto_nombre',
        'telefono',
        'fecha_ingreso',
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id_pais');
    }

    public function scopeFiltro($query, $key)
    {
        return $query->where('nombre_cliente', 'LIKE', "%{$key}%")
                     ->orWhere('nit', 'LIKE', "%{$key}%");
    }
}
