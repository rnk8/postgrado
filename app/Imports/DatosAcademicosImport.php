<?php

namespace App\Imports;

use App\Models\DatoAcademico;
use App\Models\Programa;
use App\Models\Docente;
use App\Models\CargaExcel;
use App\Models\Certificacion;
use App\Models\Tesis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Validation\Rule;

class DatosAcademicosImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use SkipsFailures;

    private $cargaExcel;
    private $registrosProcesados = 0;
    private $registrosExitosos = 0;
    private $registrosConError = 0;
    private $errores = [];

    public function __construct(CargaExcel $cargaExcel)
    {
        $this->cargaExcel = $cargaExcel;
    }

    public function model(array $row)
    {
        $this->registrosProcesados++;

        try {
            // Normalizar modalidad del programa para que cumpla el enum
            $rawModalidad = $row['cod_modalidad'] ?? '';
            $rawModalidad = strtolower($rawModalidad);
            if (in_array($rawModalidad, ['virtual', 'virt'])) {
                $modalidad = 'virtual';
            } elseif (in_array($rawModalidad, ['semipresencial', 'sem', 'semi'])) {
                $modalidad = 'semipresencial';
            } else {
                $modalidad = 'presencial';
            }

            // Buscar o crear programa, incluyendo campos obligatorios
            $programa = Programa::firstOrCreate(
                [
                    'cod_carrera' => $row['cod_carrera'],
                    'gestion_id' => $this->cargaExcel->gestion_id,
                ],
                [
                    'cod_facultad' => $row['cod_facultad'] ?? null,
                    'nombre_facultad' => $row['nombre_facultad'] ?? null,
                    'nombre_carrera' => $row['nombre_carrera'] ?? 'Programa sin nombre',
                    'cod_plan' => $row['cod_plan'] ?? null,
                    'modalidad' => $modalidad,
                ]
            );

            // Buscar o crear docente con género y gestión adecuados
            $docente = null;
            if (!empty($row['cod_doc'])) {
                // Normalizar género para Docente (Femenino o Masculino)
                $generoDoc = strtoupper(substr($row['genero_doc'] ?? 'M', 0, 1)) === 'F' ? 'F' : 'M';
                $docente = Docente::updateOrCreate(
                    [
                        'cod_doc' => $row['cod_doc'],
                        'gestion_id' => $this->cargaExcel->gestion_id,
                    ],
                    [
                        'nombre_doc' => $row['nombre_doc'] ?? 'Docente sin nombre',
                        'genero_doc' => $generoDoc,
                        'estado' => 'activo',
                    ]
                );
            }

            // Crear dato académico
            $datoAcademico = DatoAcademico::create([
                'cod_facultad' => $row['cod_facultad'] ?? null,
                'nombre_facultad' => $row['nombre_facultad'] ?? null,
                'cod_carrera' => $row['cod_carrera'] ?? null,
                'cod_plan' => $row['cod_plan'] ?? null,
                'nombre_carrera' => $row['nombre_carrera'] ?? null,
                'cod_materia_plan' => $row['cod_materia_plan'] ?? null,
                'cod_grupo' => $row['cod_grupo'] ?? null,
                'cod_edicion' => $row['cod_edicion'] ?? null,
                'cod_modalidad' => $row['cod_modalidad'] ?? null,
                'sigla_materia' => $row['sigla_materia'] ?? null,
                'nombre_materia' => $row['nombre_materia'] ?? null,
                'fecha_ini' => $this->parseDate($row['fecha_ini'] ?? null),
                'fecha_fin' => $this->parseDate($row['fecha_fin'] ?? null),
                'cod_doc' => $row['cod_doc'] ?? null,
                'nombre_doc' => $row['nombre_doc'] ?? null,
                'genero_doc' => $row['genero_doc'] ?? null,
                'nro_registro_est' => $row['nro_registro_est'] ?? null,
                'nombre_est' => $row['nombre_est'] ?? null,
                'genero_est' => $row['genero_est'] ?? null,
                'nota' => $row['nota'] ?? null,
                'acta_cerrada' => $row['acta_cerrada'] ?? null,
                'matriculado' => $row['matriculado'] ?? null,
                'nota_defensa_tfg' => $row['nota_defensa_tfg'] ?? null,
                'fecha_defensa_tfg' => $this->parseDate($row['fecha_defensa_tfg'] ?? null),
                'gestion_id' => $this->cargaExcel->gestion_id,
                'carga_excel_id' => $this->cargaExcel->id,
            ]);

            // Crear certificación si tiene fecha de defensa
            if ($datoAcademico->fecha_defensa_tfg) {
                Certificacion::crearDesdeExcel([
                    'nro_registro_est' => $datoAcademico->nro_registro_est,
                    'nombre_est' => $datoAcademico->nombre_est,
                    'genero_est' => $datoAcademico->genero_est,
                    'nota' => $datoAcademico->nota,
                    'nota_defensa_tfg' => $datoAcademico->nota_defensa_tfg,
                    'fecha_defensa_tfg' => $datoAcademico->fecha_defensa_tfg,
                ], $programa->id, $this->cargaExcel->gestion_id);

                // Crear tesis
                Tesis::crearDesdeExcel([
                    'nro_registro_est' => $datoAcademico->nro_registro_est,
                    'nombre_est' => $datoAcademico->nombre_est,
                    'fecha_defensa_tfg' => $datoAcademico->fecha_defensa_tfg,
                    'nota_defensa_tfg' => $datoAcademico->nota_defensa_tfg,
                ], $programa->id, $this->cargaExcel->gestion_id, $docente?->id);
            }

            $this->registrosExitosos++;
            return $datoAcademico;

        } catch (\Exception $e) {
            $this->registrosConError++;
            $this->errores[] = [
                'fila' => $this->registrosProcesados,
                'error' => $e->getMessage(),
                'datos' => $row,
            ];
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nro_registro_est' => 'required|string|max:20',
            'nombre_est' => 'required|string|max:255',
            'genero_est' => 'nullable|in:M,F',
            'cod_carrera' => 'required|string|max:20',
            'nombre_carrera' => 'nullable|string|max:255',
            'cod_doc' => 'nullable|string|max:20',
            'nombre_doc' => 'nullable|string|max:255',
            'nota' => 'nullable|numeric|min:0|max:100',
            'nota_defensa_tfg' => 'nullable|numeric|min:0|max:100',
            'fecha_defensa_tfg' => 'nullable|date',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nro_registro_est.required' => 'El número de registro del estudiante es obligatorio.',
            'nombre_est.required' => 'El nombre del estudiante es obligatorio.',
            'genero_est.in' => 'El género debe ser M o F.',
            'cod_carrera.required' => 'El código de carrera es obligatorio.',
            'nota.numeric' => 'La nota debe ser un número.',
            'nota.min' => 'La nota debe ser mayor o igual a 0.',
            'nota.max' => 'La nota debe ser menor o igual a 100.',
            'nota_defensa_tfg.numeric' => 'La nota de defensa debe ser un número.',
            'nota_defensa_tfg.min' => 'La nota de defensa debe ser mayor o igual a 0.',
            'nota_defensa_tfg.max' => 'La nota de defensa debe ser menor o igual a 100.',
            'fecha_defensa_tfg.date' => 'La fecha de defensa debe ser una fecha válida.',
        ];
    }

    private function parseDate($fecha)
    {
        if (empty($fecha)) {
            return null;
        }

        try {
            // Intentar varios formatos de fecha
            $formatos = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
            
            foreach ($formatos as $formato) {
                $date = \DateTime::createFromFormat($formato, $fecha);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }

            // Si es un número (Excel date serial)
            if (is_numeric($fecha)) {
                $unixDate = ($fecha - 25569) * 86400;
                return date('Y-m-d', $unixDate);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getRegistrosProcesados()
    {
        return $this->registrosProcesados;
    }

    public function getRegistrosExitosos()
    {
        return $this->registrosExitosos;
    }

    public function getRegistrosConError()
    {
        return $this->registrosConError;
    }

    public function getErrores()
    {
        return $this->errores;
    }
} 