<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('crear_docentes');
    }

    public function rules(): array
    {
        return [
            'cod_doc' => ['required', 'string', 'max:50', 'unique:docentes,cod_doc'],
            'nombre_doc' => ['required', 'string', 'max:255'],
            'genero_doc' => ['required', 'in:M,F'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'estado' => ['required', 'in:activo,inactivo'],
        ];
    }

    public function messages(): array
    {
        return [
            'cod_doc.required' => 'El código del docente es obligatorio.',
            'cod_doc.unique' => 'Ya existe un docente con ese código.',
            'nombre_doc.required' => 'El nombre es obligatorio.',
            'genero_doc.in' => 'El género debe ser M o F.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ];
    }
} 