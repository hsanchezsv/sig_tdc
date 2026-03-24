<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre_cliente'  => 'required|string|max:150',
            'id_pais'         => 'required|integer|exists:sig_paises,id_pais',
            'nit'             => 'nullable|string|max:30',
            'direccion'       => 'nullable|string|max:200',
            'contacto_nombre' => 'nullable|string|max:100',
            'telefono'        => 'nullable|string|max:20',
            'fecha_ingreso'   => 'nullable|date',
        ];
    }

    public function attributes()
    {
        return [
            'nombre_cliente'  => 'nombre del cliente',
            'id_pais'         => 'país',
            'nit'             => 'NIT/RTU',
            'contacto_nombre' => 'nombre del contacto',
            'fecha_ingreso'   => 'fecha de ingreso',
        ];
    }
}
