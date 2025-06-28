<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('crear_programas');
    }

    public function rules(): array
    {
        return [
            'cod_carrera' => ['required', 'string', 'max:50', 'unique:programas,cod_carrera'],
            'nombre_carrera' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:maestria,doctorado,especialidad'],
            'modalidad' => ['required', 'in:presencial,virtual,semipresencial'],
            'estado' => ['required', 'in:activo,inactivo'],
            'coordinador_id' => ['nullable', 'exists:docentes,id'],
        ];
    }
} 