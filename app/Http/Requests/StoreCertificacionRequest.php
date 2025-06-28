<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('crear_certificaciones');
    }

    public function rules(): array
    {
        return [
            'nro_registro_est' => 'required|string|max:20',
            'nombre_est' => 'required|string|max:255',
            'genero_est' => 'required|in:M,F',
            'nota' => 'nullable|numeric|min:0|max:100',
            'nota_defensa_tfg' => 'nullable|numeric|min:0|max:100',
            'fecha_defensa_tfg' => 'nullable|date',
            'programa_id' => 'required|exists:programas,id',
            'estado' => 'required|in:pendiente,emitido,entregado',
        ];
    }

    public function messages(): array
    {
        return [
            'nro_registro_est.required' => 'El número de registro del estudiante es obligatorio.',
            'nro_registro_est.max' => 'El número de registro no puede tener más de 20 caracteres.',
            'nombre_est.required' => 'El nombre del estudiante es obligatorio.',
            'nombre_est.max' => 'El nombre no puede tener más de 255 caracteres.',
            'genero_est.required' => 'El género del estudiante es obligatorio.',
            'genero_est.in' => 'El género debe ser M (Masculino) o F (Femenino).',
            'nota.numeric' => 'La nota debe ser un número.',
            'nota.min' => 'La nota debe ser mayor o igual a 0.',
            'nota.max' => 'La nota debe ser menor o igual a 100.',
            'nota_defensa_tfg.numeric' => 'La nota de defensa debe ser un número.',
            'nota_defensa_tfg.min' => 'La nota de defensa debe ser mayor o igual a 0.',
            'nota_defensa_tfg.max' => 'La nota de defensa debe ser menor o igual a 100.',
            'fecha_defensa_tfg.date' => 'La fecha de defensa debe ser una fecha válida.',
            'programa_id.required' => 'El programa es obligatorio.',
            'programa_id.exists' => 'El programa seleccionado no es válido.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser: pendiente, emitido o entregado.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nro_registro_est' => 'número de registro',
            'nombre_est' => 'nombre del estudiante',
            'genero_est' => 'género',
            'nota' => 'nota',
            'nota_defensa_tfg' => 'nota de defensa',
            'fecha_defensa_tfg' => 'fecha de defensa',
            'programa_id' => 'programa',
            'estado' => 'estado',
        ];
    }
} 