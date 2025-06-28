<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('editar_docentes');
    }

    public function rules(): array
    {
        $docenteId = $this->route('docente')->id ?? null;
        return [
            'cod_doc' => ['required', 'string', 'max:50', Rule::unique('docentes', 'cod_doc')->ignore($docenteId)],
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