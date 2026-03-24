<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sig_sucursales';
    protected $primaryKey = 'id_sucursal';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'id_pais',
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id_pais');
    }

    public function vendedores()
    {
        return $this->hasMany(Vendedor::class, 'id_sucursal', 'id_sucursal');
    }

    public function scopeFiltro($query, $key)
    {
        return $query->where('nombre', 'LIKE', "%{$key}%")
                     ->orWhere('codigo', 'LIKE', "%{$key}%");
    }
}
