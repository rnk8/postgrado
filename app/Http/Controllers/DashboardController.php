<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;
use App\Models\Gestion;
use App\Models\Docente;
use App\Models\Programa;
use App\Models\Certificacion;
use App\Models\Tesis;
use App\Models\User;
use App\Services\GestionService;
use App\Models\PageVisit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Controlador principal del Dashboard
 * 
 * Maneja la vista principal del sistema con estadísticas en tiempo real,
 * gráficos de resumen y actividad reciente del sistema de postgrado
 */
class DashboardController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Service para manejar operaciones de gestiones
     */
    protected GestionService $gestionService;

    /**
     * Constructor del controlador
     */
    public function __construct(GestionService $gestionService)
    {
        $this->gestionService = $gestionService;
    }

    /**
     * Mostrar el dashboard principal con estadísticas del sistema
     * 
     * Presenta un resumen completo del estado actual del sistema incluyendo:
     * - Estadísticas de la gestión actual (docentes, programas, certificaciones, tesis)
     * - Gráficos de distribución por tipo y modalidad
     * - Actividad reciente del sistema
     * - Permisos del usuario actual
     */
    public function index(Request $request)
    {
        // Verificar que el usuario tenga permisos para ver reportes/dashboard
        $this->authorize('ver_reportes');
        
        // Obtener la gestión académica actual desde la caché global
        $gestionActual = Cache::get('gestion_actual');
        
        // Si no hay gestión activa, aún podemos mostrar el Dashboard pero con una advertencia.
        // Las estadísticas específicas de la gestión estarán vacías.
        
        $estadisticas = [];
        $tesis_por_estado = [];
        $ultimas_tesis = [];

        if ($gestionActual) {
            $estadisticas = [
                'total_docentes' => Docente::where('gestion_id', $gestionActual->id)->count(),
                'total_programas' => Programa::where('gestion_id', $gestionActual->id)->count(),
                'total_tesis' => Tesis::where('gestion_id', $gestionActual->id)->count(),
                'total_certificaciones' => Certificacion::where('gestion_id', $gestionActual->id)->count(),
            ];

            $tesis_por_estado = Tesis::where('gestion_id', $gestionActual->id)
                ->select('estado', DB::raw('count(*) as total'))
                ->groupBy('estado')
                ->pluck('total', 'estado');

            $ultimas_tesis = Tesis::with(['programa', 'tutor'])
                ->where('gestion_id', $gestionActual->id)
                ->latest()
                ->take(5)
                ->get();
        }

        $paginas_mas_visitadas = PageVisit::select('route', 'visitas')
            ->orderByDesc('visitas')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard/Index', [
            'gestionActual' => $gestionActual, // Se pasa para mostrar el nombre y la advertencia
            'estadisticas' => $estadisticas,
            'tesis_por_estado' => $tesis_por_estado,
            'paginas_mas_visitadas' => $paginas_mas_visitadas,
            'ultimas_tesis' => $ultimas_tesis,
            'permisos' => [
                'puede_ver_reportes' => $request->user()->can('ver_reportes'),
                'puede_generar_reportes' => $request->user()->can('generar_reportes'),
                'puede_ver_gestiones' => $request->user()->can('ver_gestiones'),
                'puede_crear_gestiones' => $request->user()->can('crear_gestiones'),
                'puede_cargar_excel' => $request->user()->can('cargar_excel'),
            ],
        ]);
    }
    
    /**
     * Obtener estadísticas principales para las tarjetas del dashboard
     * 
     * Calcula los números principales que se muestran en las tarjetas grandes
     * del dashboard: docentes, programas, certificaciones y tesis
     */
    private function obtenerEstadisticasPrincipales(Gestion $gestion): array
    {
        // Contar docentes con estado
        $totalDocentes = Docente::where('gestion_id', $gestion->id)->count();
        $docentesActivos = Docente::where('gestion_id', $gestion->id)
            ->where('estado', 'activo')
            ->count();
                
        // Contar programas con estado
        $totalProgramas = Programa::where('gestion_id', $gestion->id)->count();
        $programasActivos = Programa::where('gestion_id', $gestion->id)
            ->where('estado', 'activo')
            ->count();
                
        // Contar certificaciones asociadas a programas de esta gestión
        $totalCertificaciones = Certificacion::whereHas('programa', function($query) use ($gestion) {
            $query->where('gestion_id', $gestion->id);
        })->count();
        
        // Contar tesis asociadas a programas de esta gestión
        $totalTesis = Tesis::whereHas('programa', function($query) use ($gestion) {
            $query->where('gestion_id', $gestion->id);
        })->count();
        
        // Contar tesis aprobadas para mostrar progreso
        $tesisAprobadas = Tesis::whereHas('programa', function($query) use ($gestion) {
            $query->where('gestion_id', $gestion->id);
        })->where('estado', 'aprobada')->count();
        
        return [
            'docentes' => [
                'total' => $totalDocentes,
                'activos' => $docentesActivos,
                'inactivos' => $totalDocentes - $docentesActivos,
                'porcentaje_activos' => $totalDocentes > 0 ? round(($docentesActivos / $totalDocentes) * 100, 1) : 0,
            ],
            'programas' => [
                'total' => $totalProgramas,
                'activos' => $programasActivos,
                'inactivos' => $totalProgramas - $programasActivos,
                'porcentaje_activos' => $totalProgramas > 0 ? round(($programasActivos / $totalProgramas) * 100, 1) : 0,
            ],
            'certificaciones' => [
                'total' => $totalCertificaciones,
                'promedio_por_programa' => $totalProgramas > 0 ? round($totalCertificaciones / $totalProgramas, 1) : 0,
            ],
            'tesis' => [
                'total' => $totalTesis,
                'aprobadas' => $tesisAprobadas,
                'pendientes' => $totalTesis - $tesisAprobadas,
                'porcentaje_aprobadas' => $totalTesis > 0 ? round(($tesisAprobadas / $totalTesis) * 100, 1) : 0,
                'promedio_por_programa' => $totalProgramas > 0 ? round($totalTesis / $totalProgramas, 1) : 0,
            ],
        ];
    }
    
    /**
     * Obtener datos para gráficos y visualizaciones del dashboard
     * 
     * Prepara los datos necesarios para mostrar gráficos de barras, líneas y torta
     * con información sobre distribución de programas, certificaciones y tesis
     */
    private function obtenerDatosGraficos(Gestion $gestion): array
    {
        // Programas por tipo (Maestría, Doctorado, Especialidad)
        $programasPorTipo = Programa::where('gestion_id', $gestion->id)
            ->where('estado', 'activo')
            ->selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst($item->tipo),
                    'value' => $item->total,
                    'color' => $this->obtenerColorParaTipo($item->tipo),
                ];
            });
            
        // Programas por modalidad (Presencial, Virtual, Semipresencial)
        $programasPorModalidad = Programa::where('gestion_id', $gestion->id)
            ->where('estado', 'activo')
            ->selectRaw('modalidad, COUNT(*) as total')
            ->groupBy('modalidad')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst($item->modalidad),
                    'value' => $item->total,
                    'color' => $this->obtenerColorParaModalidad($item->modalidad),
                ];
            });
            
        // Certificaciones por mes (últimos 6 meses)
        $certificacionesPorMes = Certificacion::whereHas('programa', function($query) use ($gestion) {
            $query->where('gestion_id', $gestion->id);
        })
        ->selectRaw('EXTRACT(MONTH FROM fecha_emision) as mes, EXTRACT(YEAR FROM fecha_emision) as año, COUNT(*) as total')
        ->where('fecha_emision', '>=', now()->subMonths(6))
        ->groupByRaw('EXTRACT(YEAR FROM fecha_emision), EXTRACT(MONTH FROM fecha_emision)')
        ->orderByRaw('EXTRACT(YEAR FROM fecha_emision), EXTRACT(MONTH FROM fecha_emision)')
        ->get()
        ->map(function ($item) {
            return [
                'mes' => $item->mes,
                'año' => $item->año,
                'total' => $item->total,
                'label' => $this->obtenerNombreMes($item->mes) . ' ' . $item->año,
            ];
        });
        
        // Tesis por estado (Aprobada, En proceso, Observada, etc.)
        $tesisPorEstado = Tesis::whereHas('programa', function($query) use ($gestion) {
            $query->where('gestion_id', $gestion->id);
        })
        ->selectRaw('estado, COUNT(*) as total')
        ->groupBy('estado')
        ->get()
        ->map(function ($item) {
            return [
                'label' => ucfirst(str_replace('_', ' ', $item->estado)),
                'value' => $item->total,
                'color' => $this->obtenerColorParaEstado($item->estado),
            ];
        });
        
        return [
            'programas_por_tipo' => $programasPorTipo,
            'programas_por_modalidad' => $programasPorModalidad,
            'certificaciones_por_mes' => $certificacionesPorMes,
            'tesis_por_estado' => $tesisPorEstado,
        ];
    }
    
    /**
     * Obtener actividad reciente del sistema
     * 
     * Muestra las últimas certificaciones, tesis y acciones realizadas
     * en el sistema para dar una vista de la actividad actual
     */
    private function obtenerActividadReciente(Gestion $gestion): array
    {
        // Certificaciones más recientes de la gestión actual
        $certificacionesRecientes = Certificacion::with(['programa'])
            ->whereHas('programa', function($query) use ($gestion) {
                $query->where('gestion_id', $gestion->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($cert) {
                return [
                    'id' => $cert->id,
                    'programa' => $cert->programa->nombre_carrera,
                    'estudiante' => $cert->nombre_est ?? 'No especificado',
                    'fecha_emision' => $cert->fecha_emision,
                    'fecha_creacion' => $cert->created_at,
                    'tipo' => 'certificacion',
                ];
            });
            
        // Tesis más recientes de la gestión actual
        $tesisRecientes = Tesis::with(['programa', 'tutor'])
            ->whereHas('programa', function($query) use ($gestion) {
                $query->where('gestion_id', $gestion->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($tesis) {
                return [
                    'id' => $tesis->id,
                    'titulo' => $tesis->titulo ?? 'Sin título',
                    'programa' => $tesis->programa->nombre_carrera,
                    'estudiante' => $tesis->nombre_est ?? 'No especificado',
                    'tutor' => $tesis->tutor->nombre_doc ?? 'Sin tutor',
                    'estado' => $tesis->estado,
                    'fecha_defensa' => $tesis->fecha_defensa_tfg,
                    'fecha_creacion' => $tesis->created_at,
                    'tipo' => 'tesis',
                ];
            });
            
        // Combinar todas las actividades y ordenar por fecha
        $actividadCombinada = $certificacionesRecientes
            ->concat($tesisRecientes)
            ->sortByDesc('fecha_creacion')
            ->take(10)
            ->values();
            
        return [
            'actividad_reciente' => $actividadCombinada,
            'certificaciones_recientes' => $certificacionesRecientes,
            'tesis_recientes' => $tesisRecientes,
        ];
    }

    /**
     * Obtener resumen general del sistema (todas las gestiones)
     */
    private function obtenerResumenSistema(): array
    {
        return [
            'total_usuarios' => User::count(),
            'usuarios_activos' => User::where('created_at', '>=', now()->subDays(30))
                ->count(),
            'total_gestiones' => Gestion::count(),
            'gestiones_activas' => Gestion::where('estado', 'activo')->count(),
            'total_docentes_sistema' => Docente::count(),
            'total_programas_sistema' => Programa::count(),
            'total_certificaciones_sistema' => Certificacion::count(),
            'total_tesis_sistema' => Tesis::count(),
        ];
    }

    /**
     * Obtener color para tipo de programa
     */
    private function obtenerColorParaTipo(string $tipo): string
    {
        $colores = [
            'maestria' => '#3B82F6',      // Azul
            'doctorado' => '#EF4444',     // Rojo
            'especialidad' => '#10B981',  // Verde
            'diplomado' => '#F59E0B',     // Amarillo/Naranja
        ];
        
        return $colores[strtolower($tipo)] ?? '#6B7280'; // Gris por defecto
    }

    /**
     * Obtener color para modalidad de programa
     */
    private function obtenerColorParaModalidad(string $modalidad): string
    {
        $colores = [
            'presencial' => '#8B5CF6',        // Púrpura
            'virtual' => '#06B6D4',           // Cian
            'semipresencial' => '#F97316',    // Naranja
            'hibrida' => '#EC4899',           // Rosa
        ];
        
        return $colores[strtolower($modalidad)] ?? '#6B7280'; // Gris por defecto
    }

    /**
     * Obtener color para estado de tesis
     */
    private function obtenerColorParaEstado(string $estado): string
    {
        $colores = [
            'aprobada' => '#10B981',      // Verde
            'en_proceso' => '#F59E0B',    // Amarillo
            'observada' => '#EF4444',     // Rojo
            'pendiente' => '#6B7280',     // Gris
            'defendida' => '#3B82F6',     // Azul
        ];
        
        return $colores[strtolower($estado)] ?? '#6B7280'; // Gris por defecto
    }

    /**
     * Obtener nombre del mes en español
     */
    private function obtenerNombreMes(int $numeroMes): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $meses[$numeroMes] ?? 'Mes desconocido';
    }
} 