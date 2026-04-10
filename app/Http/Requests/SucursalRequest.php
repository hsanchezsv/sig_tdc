<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SucursalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'codigo'  => 'required|string|max:20',
            'nombre'  => 'required|string|max:100',
            'id_pais' => 'required|integer|exists:sig_paises,id_pais',
        ];
    }

    public function attributes()
    {
        return [
            'codigo'  => 'código',
            'nombre'  => 'nombre de sucursal',
            'id_pais' => 'país',
        ];
    }
}
