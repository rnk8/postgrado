<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request para validar la creación de gestiones académicas
 * 
 * Maneja las reglas de validación para crear nuevos períodos académicos
 * como 2024-I, 2024-II, etc. con sus fechas de inicio y fin
 */
class StoreGestionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta request
     */
    public function authorize(): bool
    {
        // Solo usuarios con permiso específico pueden crear gestiones
        return $this->user()->can('crear_gestiones');
    }

    /**
     * Reglas de validación para la creación de gestiones académicas
        */
    public function rules(): array
    {
        return [
            // Nombre único de la gestión (ej: 2024-I, 2024-II)
            'nombre' => [
                'required',
                'string',
                'max:255',
                'unique:gestiones,nombre',
                // Validar formato del nombre de gestión
                'regex:/^\d{4}-(I|II)$/',
            ],
            
            // Descripción opcional de la gestión
            'descripcion' => [
                'nullable',
                'string',
                'max:500',
            ],
            
            // Fecha de inicio del período académico
            'fecha_inicio' => [
                'required',
                'date',
                'after_or_equal:' . now()->subYears(2)->format('Y-m-d'), // No más de 2 años atrás
            ],
            
            // Fecha de fin debe ser posterior al inicio
            'fecha_fin' => [
                'required',
                'date',
                'after:fecha_inicio',
                'before_or_equal:' . now()->addYears(5)->format('Y-m-d'), // No más de 5 años adelante
            ],
            
            // Estado de la gestión
            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo']),
            ],
            
            // Si se marca como gestión actual
            'es_actual' => [
                'boolean',
            ],
        ];
    }

    /**
     * Mensajes de error personalizados en español
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la gestión es obligatorio.',
            'nombre.unique' => 'Ya existe una gestión con este nombre.',
            'nombre.regex' => 'El formato del nombre debe ser YYYY-I o YYYY-II (ej: 2024-I).',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser muy antigua.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'fecha_fin.before_or_equal' => 'La fecha de fin no puede ser muy lejana.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ];
    }

    /**
     * Nombres de atributos personalizados para los mensajes de error
 
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre de la gestión',
            'descripcion' => 'descripción',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'estado' => 'estado',
            'es_actual' => 'gestión actual',
        ];
    }

    /**
     * Preparar los datos para validación
     * 
     * Limpia y formatea los datos antes de validar
     */
    protected function prepareForValidation(): void
    {
        // Limpiar espacios en blanco del nombre
        if ($this->has('nombre')) {
            $this->merge([
                'nombre' => trim($this->nombre),
            ]);
        }

        // Asegurar que es_actual sea booleano
        if ($this->has('es_actual')) {
            $this->merge([
                'es_actual' => (bool) $this->es_actual,
            ]);
        }
    }

    /**
     * Configurar el validador después de la validación inicial
 
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que las fechas no se solapen con otras gestiones
            if (!$validator->errors()->has('fecha_inicio') && !$validator->errors()->has('fecha_fin')) {
                $this->validarSolapamientoFechas($validator);
            }
        });
    }

    /**
     * Validar que las fechas no se solapen con gestiones existentes
 
     */
    private function validarSolapamientoFechas($validator): void
    {
        $fechaInicio = $this->fecha_inicio;
        $fechaFin = $this->fecha_fin;

        // Buscar gestiones que se solapen
        $solapamientos = \App\Models\Gestion::where(function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                  ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                  ->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
                      $q->where('fecha_inicio', '<=', $fechaInicio)
                        ->where('fecha_fin', '>=', $fechaFin);
                  });
        })->exists();

        if ($solapamientos) {
            $validator->errors()->add(
                'fecha_inicio', 
                'Las fechas se solapan con una gestión existente.'
            );
        }
    }
}
