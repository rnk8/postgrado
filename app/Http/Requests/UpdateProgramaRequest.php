<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('editar_programas');
    }

    public function rules(): array
    {
        $id = $this->route('programa')->id ?? null;
        return [
            'cod_carrera' => ['required', 'string', 'max:50', Rule::unique('programas','cod_carrera')->ignore($id)],
            'nombre_carrera' => ['required','string','max:255'],
            'tipo' => ['required','in:maestria,doctorado,especialidad'],
            'modalidad' => ['required','in:presencial,virtual,semipresencial'],
            'estado' => ['required','in:activo,inactivo'],
            'coordinador_id' => ['nullable','exists:docentes,id'],
        ];
    }
}
 