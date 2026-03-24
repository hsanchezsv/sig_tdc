<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fecha_compra'           => 'required|date',
            'id_sucursal'            => 'required|integer|exists:sig_sucursales,id_sucursal',
            'id_vendedor'            => 'required|integer|exists:sig_vendedores,id_vendedor',
            'items'                  => 'required|array|min:1',
            'items.*.id_producto'    => 'required|integer|exists:sig_productos,id_producto',
            'items.*.cantidad'       => 'required|integer|min:1',
            'items.*.precio_unidad'  => 'required|numeric|min:0',
            'num_factura'            => 'nullable|string|max:45',
        ];
    }

    public function attributes()
    {
        return [
            'fecha_compra'          => 'fecha de compra',
            'id_sucursal'           => 'sucursal',
            'id_vendedor'           => 'vendedor',
            'items'                 => 'items de la factura',
            'items.*.id_producto'   => 'producto',
            'items.*.cantidad'      => 'cantidad',
            'items.*.precio_unidad' => 'precio unitario',
            'num_factura'           => 'número de factura',
        ];
    }
}
