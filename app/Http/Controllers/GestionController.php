<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Gestion;
use App\Services\GestionService;
use App\Http\Requests\StoreGestionRequest;
use App\Http\Requests\UpdateGestionRequest;

/**
 * Controlador de Gestiones Académicas
 * 
 * Maneja todos los períodos académicos del sistema de postgrado (2024-I, 2024-II, etc.)
 * Permite crear, editar, activar y gestionar los datos asociados a cada gestión
 */
class GestionController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Service para manejar la lógica de negocio de gestiones
     */
    protected GestionService $gestionService;

    /**
     * Constructor del controlador
     * 
     * @param GestionService $gestionService
     */
    public function __construct(GestionService $gestionService)
    {
        $this->gestionService = $gestionService;
    }

    /**
     * Mostrar listado de gestiones académicas con filtros y paginación
     * 
     * Permite buscar por nombre, filtrar por estado y visualizar estadísticas
     * de cada período académico (cantidad de docentes y programas)
     * 
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Verificar que el usuario tenga permisos para ver gestiones
        $this->authorize('ver_gestiones');

        // Preparar filtros para la búsqueda
        $filtros = [
            'search' => $request->get('search'),
            'estado' => $request->get('estado'),
            'año' => $request->get('año'),
            'per_page' => $request->get('per_page', 15),
        ];

        // Usar el service para obtener gestiones filtradas
        $gestiones = $this->gestionService->buscarGestiones($filtros);

        // Obtener resumen general del sistema
        $resumen = $this->gestionService->obtenerResumenGestiones();

        return Inertia::render('Gestiones/Index', [
            'gestiones' => $gestiones,
            'resumen' => $resumen,
            'filters' => array_filter($filtros), // Solo enviar filtros no vacíos
            'estadosDisponibles' => $this->obtenerEstadosDisponibles(),
            'añosDisponibles' => $this->obtenerAñosDisponibles(),
            'permisos' => [
                'puede_crear' => $request->user()->can('crear_gestiones'),
                'puede_editar' => $request->user()->can('editar_gestiones'),
                'puede_eliminar' => $request->user()->can('eliminar_gestiones'),
                'puede_activar' => $request->user()->can('activar_gestiones'),
            ]
        ]);
    }

    /**
     * Mostrar formulario de creación
     * 
     * @return \Inertia\Response
     */
    public function create()
    {
        // Verificar permisos
        $this->authorize('crear_gestiones');

        return Inertia::render('Gestiones/Create', [
            'estadosDisponibles' => $this->obtenerEstadosDisponibles(),
            'gestionActual' => $this->gestionService->obtenerGestionActual(),
        ]);
    }

    /**
     * Crear y almacenar una nueva gestión académica
     * 
     * Utiliza StoreGestionRequest para validaciones robustas y GestionService
     * para manejar la lógica de negocio (como desactivar gestiones anteriores)
     * 
     * @param StoreGestionRequest $request Request con datos validados
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreGestionRequest $request)
    {
        Log::info('Intentando crear gestión', [
            'raw_data' => $request->all(),
            'user_id' => $request->user()->id
        ]);

        try {
            // Obtener datos validados
            $data = $request->validated();
            
            Log::info('Datos validados exitosamente', ['validated_data' => $data]);
            
            // Si no hay gestión actual, marcar esta como actual automáticamente
            $gestionActual = $this->gestionService->obtenerGestionActual();
            if (!$gestionActual) {
                $data['es_actual'] = true;
                $data['estado'] = 'activo';
                Log::info('No hay gestión actual, marcando como actual');
            }

            // Crear la gestión usando el service
            $gestion = $this->gestionService->crearGestion($data);

            $mensaje = $gestion->es_actual 
                ? "Gestión académica '{$gestion->nombre}' creada exitosamente y establecida como actual."
                : "Gestión académica '{$gestion->nombre}' creada exitosamente.";

            Log::info('Gestión creada exitosamente', ['gestion_id' => $gestion->id]);

            return redirect()->route('gestiones.index')
                ->with('success', $mensaje);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación', [
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Hay errores en el formulario. Por favor revísalos.');
                
        } catch (\Exception $e) {
            Log::error('Error creando gestión: ' . $e->getMessage(), [
                'data' => $request->all(),
                'user_id' => $request->user()->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la gestión: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalles completos de una gestión académica
     * 
     * Incluye estadísticas detalladas, datos de docentes, programas,
     * certificaciones y tesis asociadas al período académico
     * 
     * @param Gestion $gestion
     * @param Request $request
     * @return \Inertia\Response
     */
    public function show(Gestion $gestion, Request $request)
    {
        // Verificar que el usuario tenga permisos para ver gestiones
        $this->authorize('ver_gestiones');

        // Obtener estadísticas completas usando el service
        $estadisticas = $this->gestionService->obtenerEstadisticasGestion($gestion);
        
        // Verificar si la gestión puede ser eliminada
        $estadoEliminacion = $this->gestionService->puedeEliminar($gestion);

        return Inertia::render('Gestiones/Show', [
            'gestion' => $gestion,
            'estadisticas' => $estadisticas,
            'estadoEliminacion' => $estadoEliminacion,
            'permisos' => [
                'puede_editar' => $request->user()->can('editar_gestiones'),
                'puede_eliminar' => $request->user()->can('eliminar_gestiones'),
                'puede_activar' => $request->user()->can('activar_gestiones'),
            ]
        ]);
    }

    /**
     * Mostrar formulario de edición
     * 
     * @param Gestion $gestion
     * @return \Inertia\Response
     */
    public function edit(Gestion $gestion)
    {
        // Verificar permisos
        $this->authorize('editar_gestiones');

        return Inertia::render('Gestiones/Edit', [
            'gestion' => [
                'id' => $gestion->id,
                'nombre' => $gestion->nombre,
                'descripcion' => $gestion->descripcion,
                'fecha_inicio' => $gestion->fecha_inicio ? $gestion->fecha_inicio->format('Y-m-d') : null,
                'fecha_fin' => $gestion->fecha_fin ? $gestion->fecha_fin->format('Y-m-d') : null,
                'estado' => $gestion->estado,
                'es_actual' => $gestion->es_actual,
            ],
            'estadosDisponibles' => $this->obtenerEstadosDisponibles(),
        ]);
    }

    /**
     * Actualizar una gestión académica existente
     * 
     * Utiliza UpdateGestionRequest para validaciones especiales (como evitar
     * reducir fechas si ya hay datos) y GestionService para la lógica de negocio
     * 
     * @param UpdateGestionRequest $request Request con datos validados
     * @param Gestion $gestion Gestión a actualizar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateGestionRequest $request, Gestion $gestion)
    {
        try {
            // Actualizar usando el service con validaciones de negocio
            $gestionActualizada = $this->gestionService->actualizarGestion(
                $gestion, 
                $request->validated()
            );

            return redirect()->route('gestiones.index')
                ->with('success', "Gestión académica '{$gestionActualizada->nombre}' actualizada exitosamente.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la gestión: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar una gestión académica
     * 
     * Verifica que no sea la gestión actual y que no tenga datos asociados
     * antes de proceder con la eliminación
     * 
     * @param Gestion $gestion Gestión a eliminar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Gestion $gestion)
    {
        // Verificar que el usuario tenga permisos para eliminar gestiones
        $this->authorize('eliminar_gestiones');

        try {
            // Eliminar usando el service (incluye todas las validaciones)
            $this->gestionService->eliminarGestion($gestion);
            
            return redirect()->route('gestiones.index')
                ->with('success', "Gestión académica '{$gestion->nombre}' eliminada exitosamente.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Activar una gestión como período académico actual
     * 
     * Desactiva automáticamente cualquier otra gestión que esté marcada como actual
     * y activa la gestión seleccionada
     * 
     * @param Gestion $gestion Gestión a activar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activar(Gestion $gestion)
    {
        // Verificar que el usuario tenga permisos para activar gestiones
        $this->authorize('activar_gestiones');

        try {
            // Activar usando el service (maneja la lógica de desactivar otras)
            $this->gestionService->activarGestion($gestion);

            return redirect()->back()
                ->with('success', "Gestión '{$gestion->nombre}' activada como período académico actual.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al activar la gestión: ' . $e->getMessage());
        }
    }

    /**
     * Obtener opciones de estados disponibles para gestiones
     * 
     * @return array Opciones para select de estados
     */
    private function obtenerEstadosDisponibles(): array
    {
        return [
            ['value' => 'activo', 'label' => 'Activo'],
            ['value' => 'inactivo', 'label' => 'Inactivo'],
        ];
    }

    /**
     * Obtener años disponibles para filtrar gestiones
     * 
     * Extrae los años únicos de las gestiones existentes para el filtro
     * 
     * @return array Años disponibles en el sistema
     */
    private function obtenerAñosDisponibles(): array
    {
        // Obtener años únicos de las gestiones existentes
        $años = Gestion::selectRaw('SUBSTRING(nombre, 1, 4) as año')
            ->distinct()
            ->orderByDesc('año')
            ->pluck('año')
            ->filter() // Remover valores null
            ->map(function ($año) {
                return ['value' => $año, 'label' => $año];
            })
            ->values()
            ->toArray();

        return $años;
    }
} 