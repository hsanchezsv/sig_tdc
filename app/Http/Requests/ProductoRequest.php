<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'codigo_producto' => 'required|string|max:50',
            'nombre_producto' => 'required|string|max:150',
            'id_proveedor'    => 'required|integer|exists:sig_proveedores,id_proveedor',
            'precio_unidad'   => 'nullable|numeric|min:0',
            'id_pais'         => 'required|integer|exists:sig_paises,id_pais',
            'fecha_compra'    => 'nullable|date',
            'lote_numero'     => 'nullable|string|max:50',
        ];
    }

    public function attributes()
    {
        return [
            'codigo_producto' => 'código del producto',
            'nombre_producto' => 'nombre del producto',
            'id_proveedor'    => 'proveedor',
            'precio_unidad'   => 'precio unitario',
            'id_pais'         => 'país de origen',
            'fecha_compra'    => 'fecha de compra',
            'lote_numero'     => 'número de lote',
        ];
    }
}
