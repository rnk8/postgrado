<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Gestion;

/**
 * Request para validar la actualización de gestiones académicas
 * 
 * Maneja las reglas de validación para editar períodos académicos existentes
 * con validaciones especiales para evitar conflictos
 */
class UpdateGestionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta request
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        // Solo usuarios con permiso específico pueden editar gestiones
        return $this->user()->can('editar_gestiones');
    }

    /**
     * Reglas de validación para la actualización de gestiones académicas
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtener la gestión que se está actualizando
        $gestion = $this->route('gestion');
        
        return [
            // Nombre único excluyendo la gestión actual
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('gestiones', 'nombre')->ignore($gestion->id),
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
                'after_or_equal:' . now()->subYear()->format('Y-m-d'),
            ],
            
            // Fecha de fin debe ser posterior al inicio
            'fecha_fin' => [
                'required',
                'date',
                'after:fecha_inicio',
                'before_or_equal:' . now()->addYears(2)->format('Y-m-d'),
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
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la gestión es obligatorio.',
            'nombre.unique' => 'Ya existe otra gestión con este nombre.',
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
     * 
     * @return array<string, string>
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
     * 
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que las fechas no se solapen con otras gestiones
            if (!$validator->errors()->has('fecha_inicio') && !$validator->errors()->has('fecha_fin')) {
                $this->validarSolapamientoFechas($validator);
            }
            
            // Validar reglas especiales para gestión actual
            $this->validarGestionActual($validator);
        });
    }

    /**
     * Validar que las fechas no se solapen con gestiones existentes
     * 
     * @param \Illuminate\Validation\Validator $validator
     */
    private function validarSolapamientoFechas($validator): void
    {
        $fechaInicio = $this->fecha_inicio;
        $fechaFin = $this->fecha_fin;
        $gestionActual = $this->route('gestion');

        // Buscar gestiones que se solapen (excluyendo la actual)
        $solapamientos = Gestion::where('id', '!=', $gestionActual->id)
            ->where(function ($query) use ($fechaInicio, $fechaFin) {
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
                'Las fechas se solapan con otra gestión existente.'
            );
        }
    }

    /**
     * Validar reglas especiales para gestión actual
     * 
     * @param \Illuminate\Validation\Validator $validator
     */
    private function validarGestionActual($validator): void
    {
        $gestionActual = $this->route('gestion');
        
        // Si se está desactivando una gestión que es actual
        if ($gestionActual->es_actual && $this->estado === 'inactivo') {
            $validator->errors()->add(
                'estado', 
                'No se puede desactivar la gestión actual. Primero active otra gestión.'
            );
        }
        
        // Si hay datos asociados, validar cambios críticos
        $tieneDatos = $gestionActual->docentes()->exists() || $gestionActual->programas()->exists();
        
        if ($tieneDatos) {
            // No permitir cambios drásticos en fechas si ya hay datos
            $cambioFechaInicio = $gestionActual->fecha_inicio->format('Y-m-d') !== $this->fecha_inicio;
            $cambioFechaFin = $gestionActual->fecha_fin->format('Y-m-d') !== $this->fecha_fin;
            
            if ($cambioFechaInicio || $cambioFechaFin) {
                // Permitir solo extensión de fechas, no reducción
                if ($this->fecha_inicio > $gestionActual->fecha_inicio || 
                    $this->fecha_fin < $gestionActual->fecha_fin) {
                    $validator->errors()->add(
                        'fecha_inicio', 
                        'No se pueden reducir las fechas de una gestión que ya tiene datos asociados.'
                    );
                }
            }
        }
    }
}
