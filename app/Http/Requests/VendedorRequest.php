<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendedorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'codigo_vendedor'  => 'required|string|max:30',
            'nombre_vendedor'  => 'required|string|max:150',
            'fecha_ingreso'    => 'nullable|date',
            'numero_documento' => 'nullable|string|max:30',
            'id_sucursal'      => 'required|integer|exists:sig_sucursales,id_sucursal',
        ];
    }

    public function attributes()
    {
        return [
            'codigo_vendedor'  => 'código del vendedor',
            'nombre_vendedor'  => 'nombre del vendedor',
            'fecha_ingreso'    => 'fecha de ingreso',
            'numero_documento' => 'número de documento',
            'id_sucursal'      => 'sucursal',
        ];
    }
}
