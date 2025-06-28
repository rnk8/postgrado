<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTesisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('crear_tesis');
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:500',
            'nro_registro_est' => 'required|string|max:20',
            'nombre_est' => 'required|string|max:255',
            'fecha_defensa_tfg' => 'nullable|date',
            'nota_defensa_tfg' => 'nullable|numeric|min:0|max:100',
            'estado' => 'required|in:en_desarrollo,defendida,aprobada,rechazada',
            'tutor_id' => 'nullable|exists:docentes,id',
            'programa_id' => 'required|exists:programas,id',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título de la tesis es obligatorio.',
            'titulo.max' => 'El título no puede tener más de 500 caracteres.',
            'nro_registro_est.required' => 'El número de registro del estudiante es obligatorio.',
            'nro_registro_est.max' => 'El número de registro no puede tener más de 20 caracteres.',
            'nombre_est.required' => 'El nombre del estudiante es obligatorio.',
            'nombre_est.max' => 'El nombre no puede tener más de 255 caracteres.',
            'fecha_defensa_tfg.date' => 'La fecha de defensa debe ser una fecha válida.',
            'nota_defensa_tfg.numeric' => 'La nota de defensa debe ser un número.',
            'nota_defensa_tfg.min' => 'La nota de defensa debe ser mayor o igual a 0.',
            'nota_defensa_tfg.max' => 'La nota de defensa debe ser menor o igual a 100.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser: en desarrollo, defendida, aprobada o rechazada.',
            'tutor_id.exists' => 'El tutor seleccionado no es válido.',
            'programa_id.required' => 'El programa es obligatorio.',
            'programa_id.exists' => 'El programa seleccionado no es válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'nro_registro_est' => 'número de registro',
            'nombre_est' => 'nombre del estudiante',
            'fecha_defensa_tfg' => 'fecha de defensa',
            'nota_defensa_tfg' => 'nota de defensa',
            'estado' => 'estado',
            'tutor_id' => 'tutor',
            'programa_id' => 'programa',
        ];
    }
} 