<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'sig_ventas';
    protected $primaryKey = 'id_venta';
    public $timestamps = false;

    protected $fillable = [
        'fecha_compra',
        'num_factura',
        'id_producto',
        'id_vendedor',
        'monto_facturado',
        'id_sucursal',
        'id_promocion',
        'cantidad',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class, 'id_vendedor', 'id_vendedor');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
