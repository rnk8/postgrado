<?php

namespace App\Http\Controllers;

use App\Models\DatoAcademico;
use App\Models\Programa;
use App\Models\Docente;
use App\Models\Tesis;
use App\Models\Gestion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
class ReportController extends BaseController
{
    /**
     * Mostrar página de reportes disponibles
     */
    public function index()
    {
        $gestiones = Gestion::orderBy('es_actual', 'desc')
                           ->orderBy('fecha_inicio', 'desc')
                           ->get();
        
        $programas = Programa::with('gestion')
                           ->select('cod_facultad', 'nombre_facultad')
                           ->distinct()
                           ->orderBy('nombre_facultad')
                           ->get();

        return Inertia::render('Reportes/Index', [
            'gestiones' => $gestiones,
            'facultades' => $programas
        ]);
    }

    /**
     * Generar Reporte 1: Formulario de Informe Anual
     */
    public function informeAnual(Request $request)
    {
        $gestionId = $request->get('gestion_id');
        $programaId = $request->get('programa_id');
        
        $gestion = Gestion::find($gestionId);
        $programa = Programa::find($programaId);
        
        if (!$gestion || !$programa) {
            return back()->with('error', 'Seleccione una gestión y programa válidos');
        }

        // Obtener datos de estudiantes del programa
        $estudiantes = DatoAcademico::where('gestion_id', $gestionId)
                                   ->where('cod_carrera', $programa->cod_carrera)
                                   ->select('nro_registro_est', 'nombre_est', 'genero_est')
                                   ->distinct()
                                   ->get()
                                   ->map(function($est) use ($gestionId, $programa) {
                                       // Obtener fechas de ingreso, conclusión y tesis
                                       $datos = DatoAcademico::where('nro_registro_est', $est->nro_registro_est)
                                                            ->where('gestion_id', $gestionId)
                                                            ->where('cod_carrera', $programa->cod_carrera)
                                                            ->get();
                                       
                                       $fechaIngreso = $datos->min('fecha_ini');
                                       $fechaConclusion = $datos->where('acta_cerrada', 'S')->max('fecha_fin');
                                       $fechaTesis = $datos->whereNotNull('fecha_defensa_tfg')->first()?->fecha_defensa_tfg;
                                       
                                       // Calcular permanencia
                                       $permanenciaEscolaridad = null;
                                       $permanenciaConTesis = null;
                                       
                                       if ($fechaIngreso) {
                                           $inicio = Carbon::parse($fechaIngreso);
                                           
                                           if ($fechaConclusion) {
                                               $fin = Carbon::parse($fechaConclusion);
                                               $permanenciaEscolaridad = round($inicio->diffInMonths($fin) / 12, 2);
                                           }
                                           
                                           if ($fechaTesis) {
                                               $finTesis = Carbon::parse($fechaTesis);
                                               $permanenciaConTesis = round($inicio->diffInMonths($finTesis) / 12, 2);
                                           }
                                       }
                                       
                                       return [
                                           'registro' => $est->nro_registro_est,
                                           'nombre' => $est->nombre_est,
                                           'genero' => $est->genero_est,
                                           'fecha_ingreso' => $fechaIngreso ? Carbon::parse($fechaIngreso)->format('d/m/Y') : null,
                                           'fecha_conclusion' => $fechaConclusion ? Carbon::parse($fechaConclusion)->format('d/m/Y') : 'NO CONCLUYÓ',
                                           'fecha_tesis' => $fechaTesis ? Carbon::parse($fechaTesis)->format('d/m/Y') : null,
                                           'permanencia_escolaridad' => $permanenciaEscolaridad ?? 'N/C',
                                           'permanencia_con_tesis' => $permanenciaConTesis ?? null
                                       ];
                                   });

        $data = [
            'gestion' => $gestion,
            'programa' => $programa,
            'estudiantes' => $estudiantes,
            'fecha_generacion' => now()->format('d/m/Y H:i')
        ];

        if ($request->get('format') === 'pdf') {
            $pdf = PDF::loadView('reportes.informe-anual', $data);
            return $pdf->download('informe-anual-' . $programa->cod_carrera . '-' . $gestion->nombre . '.pdf');
        }

        return Inertia::render('Reportes/InformeAnual', $data);
    }

    /**
     * Generar Reporte 2: Resumen Programas por UPG Facultativa
     */
    public function resumenProgramas(Request $request)
    {
        $gestionId = $request->get('gestion_id');
        $codFacultad = $request->get('cod_facultad');
        
        $gestion = Gestion::find($gestionId);
        
        if (!$gestion) {
            return back()->with('error', 'Seleccione una gestión válida');
        }

        // Obtener programas de la facultad con conteo de estudiantes
        $programas = Programa::where('gestion_id', $gestionId)
                           ->when($codFacultad, function($q) use ($codFacultad) {
                               return $q->where('cod_facultad', $codFacultad);
                           })
                           ->get()
                           ->map(function($programa) use ($gestionId) {
                               $estudiantes = DatoAcademico::where('gestion_id', $gestionId)
                                                         ->where('cod_carrera', $programa->cod_carrera)
                                                         ->select('nro_registro_est', 'genero_est')
                                                         ->distinct()
                                                         ->get();
                               
                               $hombres = $estudiantes->where('genero_est', 'M')->count();
                               $mujeres = $estudiantes->where('genero_est', 'F')->count();
                               
                               return [
                                   'codigo' => $programa->cod_carrera,
                                   'nombre' => $programa->nombre_carrera,
                                   'version' => $programa->cod_plan ?? 1,
                                   'edicion' => 1, // Asumiendo edición 1
                                   'hombres' => $hombres,
                                   'mujeres' => $mujeres,
                                   'total' => $hombres + $mujeres
                               ];
                           });

        $facultad = $programas->first();

        $data = [
            'gestion' => $gestion,
            'facultad' => $facultad ? $facultad['nombre'] : 'Todas las Facultades',
            'cod_facultad' => $codFacultad,
            'programas' => $programas,
            'fecha_generacion' => now()->format('d/m/Y H:i')
        ];

        if ($request->get('format') === 'pdf') {
            $pdf = PDF::loadView('reportes.resumen-programas', $data);
            return $pdf->download('resumen-programas-' . ($codFacultad ?? 'todas') . '-' . $gestion->nombre . '.pdf');
        }

        return Inertia::render('Reportes/ResumenProgramas', $data);
    }

    /**
     * Generar Reporte 3: Estado de Alumnos
     */
    public function estadoAlumnos(Request $request)
    {
        $gestionId = $request->get('gestion_id');
        $programaId = $request->get('programa_id');
        
        $gestion = Gestion::find($gestionId);
        $programa = Programa::find($programaId);
        
        if (!$gestion) {
            return back()->with('error', 'Seleccione una gestión válida');
        }

        // Obtener estado de alumnos por programa
        $estudiantes = DatoAcademico::where('gestion_id', $gestionId)
                                   ->when($programaId, function($q) use ($programa) {
                                       return $q->where('cod_carrera', $programa->cod_carrera);
                                   })
                                   ->select('nro_registro_est', 'nombre_est', 'genero_est', 'cod_carrera')
                                   ->distinct()
                                   ->get()
                                   ->map(function($est) use ($gestionId) {
                                       $materias = DatoAcademico::where('nro_registro_est', $est->nro_registro_est)
                                                              ->where('gestion_id', $gestionId)
                                                              ->where('cod_carrera', $est->cod_carrera);
                                       
                                       $totalMaterias = $materias->distinct('sigla_materia')->count();
                                       $cursadas = $materias->where('acta_cerrada', 'S')->distinct('sigla_materia')->count();
                                       $pendientes = $totalMaterias - $cursadas;
                                       
                                       $programa = Programa::where('cod_carrera', $est->cod_carrera)
                                                         ->where('gestion_id', $gestionId)
                                                         ->first();
                                       
                                       return [
                                           'registro' => $est->nro_registro_est,
                                           'nombre' => $est->nombre_est,
                                           'genero' => $est->genero_est,
                                           'programa_codigo' => $est->cod_carrera,
                                           'programa_nombre' => $programa?->nombre_carrera,
                                           'version' => $programa?->cod_plan ?? 1,
                                           'edicion' => 1,
                                           'total_materias' => $totalMaterias,
                                           'cursadas' => $cursadas,
                                           'pendientes' => $pendientes
                                       ];
                                   });

        $data = [
            'gestion' => $gestion,
            'programa' => $programa,
            'estudiantes' => $estudiantes,
            'fecha_generacion' => now()->format('d/m/Y H:i')
        ];

        if ($request->get('format') === 'pdf') {
            $pdf = PDF::loadView('reportes.estado-alumnos', $data);
            return $pdf->download('estado-alumnos-' . ($programa?->cod_carrera ?? 'todos') . '-' . $gestion->nombre . '.pdf');
        }

        return Inertia::render('Reportes/EstadoAlumnos', $data);
    }

    /**
     * Generar Reporte 4: Reporte de Docentes
     */
    public function reporteDocentes(Request $request)
    {
        $gestionId = $request->get('gestion_id');
        $docenteId = $request->get('docente_id');
        
        $gestion = Gestion::find($gestionId);
        
        if (!$gestion) {
            return back()->with('error', 'Seleccione una gestión válida');
        }

        // Obtener actividades de docentes
        $actividades = DatoAcademico::where('gestion_id', $gestionId)
                                   ->whereNotNull('cod_doc')
                                   ->when($docenteId, function($q) use ($docenteId) {
                                       $docente = Docente::find($docenteId);
                                       return $q->where('cod_doc', $docente->cod_doc);
                                   })
                                   ->get()
                                   ->map(function($actividad) use ($gestionId) {
                                       $programa = Programa::where('cod_carrera', $actividad->cod_carrera)
                                                         ->where('gestion_id', $gestionId)
                                                         ->first();
                                       
                                       return [
                                           'cod_docente' => $actividad->cod_doc,
                                           'nombre_docente' => $actividad->nombre_doc,
                                           'genero' => $actividad->genero_doc,
                                           'programa_codigo' => $actividad->cod_carrera,
                                           'programa_nombre' => $programa?->nombre_carrera,
                                           'version' => $programa?->cod_plan ?? 1,
                                           'edicion' => 1,
                                           'sigla_materia' => $actividad->sigla_materia,
                                           'nombre_materia' => $actividad->nombre_materia,
                                           'fecha_inicio' => $actividad->fecha_ini ? Carbon::parse($actividad->fecha_ini)->format('d/m/Y') : null,
                                           'fecha_final' => $actividad->fecha_fin ? Carbon::parse($actividad->fecha_fin)->format('d/m/Y') : null
                                       ];
                                   });

        $data = [
            'gestion' => $gestion,
            'actividades' => $actividades,
            'fecha_generacion' => now()->format('d/m/Y H:i')
        ];

        if ($request->get('format') === 'pdf') {
            $pdf = PDF::loadView('reportes.reporte-docentes', $data);
            return $pdf->download('reporte-docentes-' . $gestion->nombre . '.pdf');
        }

        return Inertia::render('Reportes/ReporteDocentes', $data);
    }

    /**
     * Generar Reporte 5: Resumen Defensas de TFG/TESIS
     */
    public function resumenDefensas(Request $request)
    {
        $gestionId = $request->get('gestion_id');
        $codFacultad = $request->get('cod_facultad');
        
        $gestion = Gestion::find($gestionId);
        
        if (!$gestion) {
            return back()->with('error', 'Seleccione una gestión válida');
        }

        // Obtener defensas por programa
        $defensas = Programa::where('gestion_id', $gestionId)
                          ->when($codFacultad, function($q) use ($codFacultad) {
                              return $q->where('cod_facultad', $codFacultad);
                          })
                          ->get()
                          ->map(function($programa) use ($gestionId) {
                              $tesisDefendidas = DatoAcademico::where('gestion_id', $gestionId)
                                                             ->where('cod_carrera', $programa->cod_carrera)
                                                             ->whereNotNull('fecha_defensa_tfg')
                                                             ->select('nro_registro_est', 'genero_est')
                                                             ->distinct()
                                                             ->get();
                              
                              $hombres = $tesisDefendidas->where('genero_est', 'M')->count();
                              $mujeres = $tesisDefendidas->where('genero_est', 'F')->count();
                              
                              return [
                                  'codigo' => $programa->cod_carrera,
                                  'nombre' => $programa->nombre_carrera,
                                  'version' => $programa->cod_plan ?? 1,
                                  'edicion' => 1,
                                  'hombres' => $hombres,
                                  'mujeres' => $mujeres,
                                  'total' => $hombres + $mujeres
                              ];
                          })
                          ->filter(function($item) {
                              return $item['total'] > 0;
                          });

        $facultad = $defensas->first();

        $data = [
            'gestion' => $gestion,
            'facultad' => $facultad ? $facultad['nombre'] : 'Todas las Facultades',
            'cod_facultad' => $codFacultad,
            'defensas' => $defensas,
            'fecha_generacion' => now()->format('d/m/Y H:i')
        ];

        if ($request->get('format') === 'pdf') {
            $pdf = PDF::loadView('reportes.resumen-defensas', $data);
            return $pdf->download('resumen-defensas-' . ($codFacultad ?? 'todas') . '-' . $gestion->nombre . '.pdf');
        }

        return Inertia::render('Reportes/ResumenDefensas', $data);
    }

    /**
     * Obtener datos para llenar los select de los filtros
     */
    public function getData(Request $request)
    {
        $tipo = $request->get('tipo');
        $gestionId = $request->get('gestion_id');

        switch ($tipo) {
            case 'programas':
                $programas = Programa::where('gestion_id', $gestionId)
                                   ->orderBy('nombre_carrera')
                                   ->get();
                return response()->json($programas);

            case 'docentes':
                $docentes = Docente::where('gestion_id', $gestionId)
                                 ->orderBy('nombre_doc')
                                 ->get();
                return response()->json($docentes);

            case 'facultades':
                $facultades = Programa::where('gestion_id', $gestionId)
                                    ->select('cod_facultad', 'nombre_facultad')
                                    ->distinct()
                                    ->orderBy('nombre_facultad')
                                    ->get();
                return response()->json($facultades);

            default:
                return response()->json([]);
        }
    }
} 