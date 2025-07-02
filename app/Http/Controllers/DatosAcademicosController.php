<?php

namespace App\Http\Controllers;

use App\Models\DatoAcademico;
use App\Models\Gestion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Cache;

class DatosAcademicosController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(Request $request)
    {
        $this->authorize('ver_datos_academicos');

        $gestionActual = Cache::get('gestion_actual');
        $query = DatoAcademico::with(['gestion', 'cargaExcel']);

        if ($gestionActual) {
            $query->where('gestion_id', $gestionActual->id);
        }

        // Filtros
        if ($busqueda = $request->get('search')) {
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre_est', 'like', "%{$busqueda}%")
                  ->orWhere('nro_registro_est', 'like', "%{$busqueda}%")
                  ->orWhere('cod_carrera', 'like', "%{$busqueda}%")
                  ->orWhere('nombre_carrera', 'like', "%{$busqueda}%")
                  ->orWhere('cod_doc', 'like', "%{$busqueda}%")
                  ->orWhere('nombre_doc', 'like', "%{$busqueda}%");
            });
        }

        if ($carrera = $request->get('carrera')) {
            $query->where('cod_carrera', $carrera);
        }

        if ($conDefensa = $request->get('con_defensa')) {
            if ($conDefensa === 'si') {
                $query->whereNotNull('fecha_defensa_tfg');
            } elseif ($conDefensa === 'no') {
                $query->whereNull('fecha_defensa_tfg');
            }
        }

        if ($matriculado = $request->get('matriculado')) {
            $query->where('matriculado', $matriculado === 'si' ? 'S' : 'N');
        }

        $datosAcademicos = $query->latest()->paginate(15)->withQueryString();

        // Obtener carreras para filtro
        $carrerasQuery = DatoAcademico::query();
        if ($gestionActual) {
            $carrerasQuery->where('gestion_id', $gestionActual->id);
        }
        $carreras = $carrerasQuery->select('cod_carrera', 'nombre_carrera')
            ->distinct()
            ->orderBy('nombre_carrera')
            ->get();

        // Estadísticas
        $statsQuery = $gestionActual 
            ? DatoAcademico::where('gestion_id', $gestionActual->id) 
            : DatoAcademico::query();

        $estadisticas = [
            'total_registros' => $statsQuery->clone()->count(),
            'estudiantes_unicos' => $statsQuery->clone()->distinct('nro_registro_est')->count(),
            'con_defensa_tesis' => $statsQuery->clone()->whereNotNull('fecha_defensa_tfg')->count(),
            'matriculados' => $statsQuery->clone()->where('matriculado', 'S')->count(),
            'acta_cerrada' => $statsQuery->clone()->where('acta_cerrada', 'S')->count(),
            'carreras_activas' => $statsQuery->clone()->distinct('cod_carrera')->count(),
            'docentes_activos' => $statsQuery->clone()->whereNotNull('cod_doc')->distinct('cod_doc')->count(),
        ];

        return Inertia::render('DatosAcademicos/Index', [
            'datosAcademicos' => $datosAcademicos,
            'estadisticas' => $estadisticas,
            'carreras' => $carreras,
            'filters' => [
                'search' => $busqueda,
                'carrera' => $carrera,
                'con_defensa' => $conDefensa,
                'matriculado' => $matriculado,
            ],
            'permisos' => [
                'puede_ver' => $request->user()->can('ver_datos_academicos'),
                'puede_exportar' => $request->user()->can('exportar_datos_academicos'),
            ]
        ]);
    }

    public function show(Request $request, DatoAcademico $datoAcademico)
    {
        $this->authorize('ver_datos_academicos');

        $datoAcademico->load(['gestion', 'cargaExcel']);

        // Obtener entidades relacionadas usando los métodos del modelo
        $programa = $datoAcademico->obtenerPrograma();
        $docente = $datoAcademico->obtenerDocente();
        $certificacion = $datoAcademico->obtenerCertificacion();
        $tesis = $datoAcademico->obtenerTesis();

        // Otros estudiantes del mismo programa en la misma gestión
        $companerosPrograma = DatoAcademico::where('cod_carrera', $datoAcademico->cod_carrera)
            ->where('gestion_id', $datoAcademico->gestion_id)
            ->where('id', '!=', $datoAcademico->id)
            ->distinct('nro_registro_est')
            ->limit(10)
            ->get(['nro_registro_est', 'nombre_est']);

        return Inertia::render('DatosAcademicos/Show', [
            'datoAcademico' => $datoAcademico,
            'programa' => $programa,
            'docente' => $docente,
            'certificacion' => $certificacion,
            'tesis' => $tesis,
            'companerosPrograma' => $companerosPrograma,
            'permisos' => [
                'puede_ver' => $request->user()->can('ver_datos_academicos'),
                'puede_exportar' => $request->user()->can('exportar_datos_academicos'),
            ]
        ]);
    }

    public function estudiante(Request $request, $nroRegistro)
    {
        $this->authorize('ver_datos_academicos');

        $gestionActual = Cache::get('gestion_actual');
        $query = DatoAcademico::where('nro_registro_est', $nroRegistro)
            ->with(['gestion', 'cargaExcel']);

        if ($gestionActual) {
            $query->where('gestion_id', $gestionActual->id);
        }

        $datosEstudiante = $query->orderBy('created_at')->get();

        if ($datosEstudiante->isEmpty()) {
            abort(404, 'Estudiante no encontrado');
        }

        $primerDato = $datosEstudiante->first();
        
        // Datos del estudiante consolidados
        $estudiante = [
            'nro_registro_est' => $primerDato->nro_registro_est,
            'nombre_est' => $primerDato->nombre_est,
            'genero_est' => $primerDato->genero_est,
            'total_materias' => $datosEstudiante->count(),
            'materias_aprobadas' => $datosEstudiante->where('nota', '>=', 51)->count(),
            'promedio_general' => $datosEstudiante->whereNotNull('nota')->avg('nota'),
            'tiene_defensa' => $datosEstudiante->whereNotNull('fecha_defensa_tfg')->isNotEmpty(),
            'fecha_defensa' => $datosEstudiante->whereNotNull('fecha_defensa_tfg')->first()?->fecha_defensa_tfg,
            'nota_defensa' => $datosEstudiante->whereNotNull('nota_defensa_tfg')->first()?->nota_defensa_tfg,
        ];

        return Inertia::render('DatosAcademicos/Estudiante', [
            'estudiante' => $estudiante,
            'datosAcademicos' => $datosEstudiante,
            'permisos' => [
                'puede_ver' => $request->user()->can('ver_datos_academicos'),
                'puede_exportar' => $request->user()->can('exportar_datos_academicos'),
            ]
        ]);
    }
} 