<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProveedorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_proveedor' => 'required|string|max:150',
            'id_pais'          => 'required|integer|exists:sig_paises,id_pais',
            'direccion'        => 'nullable|string|max:200',
            'telefono'         => 'nullable|string|max:20',
            'nombre_contacto'  => 'nullable|string|max:100',
        ];
    }

    public function attributes()
    {
        return [
            'nombre_proveedor' => 'nombre del proveedor',
            'id_pais'          => 'país',
            'nombre_contacto'  => 'nombre del contacto',
        ];
    }
}
