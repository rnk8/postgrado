<?php

namespace App\Services;

use App\Models\Gestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * Service para manejar la lógica de negocio de Gestiones Académicas
 * 
 * Esta clase centraliza todas las operaciones complejas relacionadas con
 * los períodos académicos del sistema de postgrado
 */
class GestionService
{
    /**
     * Crear una nueva gestión académica
     * 
     * @param array $data Datos validados de la gestión
     * @return Gestion
     */
    public function crearGestion(array $data): Gestion
    {
        return DB::transaction(function () use ($data) {
            // Si se marca como actual, desactivar las demás
            if ($data['es_actual'] ?? false) {
                $this->desactivarTodasLasGestiones();
            }

            // Crear la nueva gestión
            return Gestion::create($data);
        });
    }

    /**
     * Actualizar una gestión académica existente
     * 
     * @param Gestion $gestion Gestión a actualizar
     * @param array $data Datos validados
     * @return Gestion
     */
    public function actualizarGestion(Gestion $gestion, array $data): Gestion
    {
        return DB::transaction(function () use ($gestion, $data) {
            // Si se marca como actual, desactivar las demás
            if ($data['es_actual'] ?? false) {
                $this->desactivarTodasLasGestionesExcepto($gestion->id);
            }

            // Actualizar la gestión
            $gestion->update($data);
            
            return $gestion->fresh();
        });
    }

    /**
     * Activar una gestión como período actual
     * 
     * @param Gestion $gestion Gestión a activar
     * @return Gestion
     */
    public function activarGestion(Gestion $gestion): Gestion
    {
        return DB::transaction(function () use ($gestion) {
            // Desactivar todas las gestiones actuales
            $this->desactivarTodasLasGestiones();
            
            // Activar esta gestión
            $gestion->update([
                'es_actual' => true,
                'estado' => 'activo'
            ]);

            return $gestion->fresh();
        });
    }

    /**
     * Eliminar una gestión académica
     * 
     * @param Gestion $gestion Gestión a eliminar
     * @return bool
     * @throws \Exception Si no se puede eliminar
     */
    public function eliminarGestion(Gestion $gestion): bool
    {
        // Verificar que no sea la gestión actual
        if ($gestion->es_actual) {
            throw new \Exception('No se puede eliminar la gestión actual.');
        }

        // Verificar que no tenga datos asociados
        if ($this->tienesDatosAsociados($gestion)) {
            throw new \Exception('No se puede eliminar una gestión que tiene datos asociados.');
        }

        return $gestion->delete();
    }

    /**
     * Obtener la gestión académica actual
     * 
     * @return Gestion|null
     */
    public function obtenerGestionActual(): ?Gestion
    {
        return Gestion::where('es_actual', true)->first();
    }

    /**
     * Obtener estadísticas detalladas de una gestión
     * 
     * @param Gestion $gestion Gestión para obtener estadísticas
     * @return array
     */
    public function obtenerEstadisticasGestion(Gestion $gestion): array
    {
        return [
            'datos_basicos' => $this->obtenerDatosBasicos($gestion),
            'docentes' => $this->obtenerEstadisticasDocentes($gestion),
            'programas' => $this->obtenerEstadisticasProgramas($gestion),
            'certificaciones' => $this->obtenerEstadisticasCertificaciones($gestion),
            'tesis' => $this->obtenerEstadisticasTesis($gestion),
        ];
    }

    /**
     * Buscar gestiones con filtros
     * 
     * @param array $filtros Filtros a aplicar
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function buscarGestiones(array $filtros = [])
    {
        $query = Gestion::query();

        // Filtro por búsqueda de texto
        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        // Filtro por año
        if (!empty($filtros['año'])) {
            $query->where('nombre', 'like', "{$filtros['año']}-%");
        }

        // Incluir conteos de relaciones
        $query->withCount(['docentes', 'programas']);

        // Ordenar por gestión actual primero, luego por fecha
        return $query->orderByDesc('es_actual')
                    ->orderByDesc('fecha_inicio')
                    ->paginate($filtros['per_page'] ?? 15);
    }

    /**
     * Verificar si una gestión puede ser eliminada
     * 
     * @param Gestion $gestion Gestión a verificar
     * @return array Estado de eliminación
     */
    public function puedeEliminar(Gestion $gestion): array
    {
        $resultado = [
            'puede_eliminar' => true,
            'razones' => []
        ];

        // No se puede eliminar la gestión actual
        if ($gestion->es_actual) {
            $resultado['puede_eliminar'] = false;
            $resultado['razones'][] = 'Es la gestión académica actual';
        }

        // No se puede eliminar si tiene datos asociados
        if ($this->tienesDatosAsociados($gestion)) {
            $resultado['puede_eliminar'] = false;
            $resultado['razones'][] = 'Tiene docentes o programas asociados';
        }

        return $resultado;
    }

    /**
     * Obtener resumen de todas las gestiones
     * 
     * @return array
     */
    public function obtenerResumenGestiones(): array
    {
        $gestiones = Gestion::withCount(['docentes', 'programas'])->get();
        
        return [
            'total_gestiones' => $gestiones->count(),
            'gestion_actual' => $gestiones->where('es_actual', true)->first(),
            'gestiones_activas' => $gestiones->where('estado', 'activo')->count(),
            'gestiones_inactivas' => $gestiones->where('estado', 'inactivo')->count(),
            'total_docentes' => $gestiones->sum('docentes_count'),
            'total_programas' => $gestiones->sum('programas_count'),
        ];
    }

    /**
     * Desactivar todas las gestiones actuales
     * 
     * @return void
     */
    private function desactivarTodasLasGestiones(): void
    {
        Gestion::where('es_actual', true)->update(['es_actual' => false]);
    }

    /**
     * Desactivar todas las gestiones excepto una específica
     * 
     * @param int $excluirId ID de la gestión a excluir
     * @return void
     */
    private function desactivarTodasLasGestionesExcepto(int $excluirId): void
    {
        Gestion::where('es_actual', true)
               ->where('id', '!=', $excluirId)
               ->update(['es_actual' => false]);
    }

    /**
     * Verificar si una gestión tiene datos asociados
     * 
     * @param Gestion $gestion Gestión a verificar
     * @return bool
     */
    private function tienesDatosAsociados(Gestion $gestion): bool
    {
        return $gestion->docentes()->exists() || $gestion->programas()->exists();
    }

    /**
     * Obtener datos básicos de una gestión
     * 
     * @param Gestion $gestion
     * @return array
     */
    private function obtenerDatosBasicos(Gestion $gestion): array
    {
        return [
            'id' => $gestion->id,
            'nombre' => $gestion->nombre,
            'descripcion' => $gestion->descripcion,
            'fecha_inicio' => $gestion->fecha_inicio,
            'fecha_fin' => $gestion->fecha_fin,
            'estado' => $gestion->estado,
            'es_actual' => $gestion->es_actual,
            'duracion_dias' => $gestion->fecha_inicio->diffInDays($gestion->fecha_fin),
        ];
    }

    /**
     * Obtener estadísticas de docentes de una gestión
     * 
     * @param Gestion $gestion
     * @return array
     */
    private function obtenerEstadisticasDocentes(Gestion $gestion): array
    {
        return [
            'total' => $gestion->docentes()->count(),
            'activos' => $gestion->docentes()->where('estado', 'activo')->count(),
            'inactivos' => $gestion->docentes()->where('estado', 'inactivo')->count(),
            'por_genero' => $gestion->docentes()
                ->selectRaw('genero_doc, COUNT(*) as total')
                ->groupBy('genero_doc')
                ->get()
                ->pluck('total', 'genero_doc')
                ->toArray(),
        ];
    }

    /**
     * Obtener estadísticas de programas de una gestión
     * 
     * @param Gestion $gestion
     * @return array
     */
    private function obtenerEstadisticasProgramas(Gestion $gestion): array
    {
        return [
            'total' => $gestion->programas()->count(),
            'activos' => $gestion->programas()->where('estado', 'activo')->count(),
            'por_tipo' => $gestion->programas()
                ->selectRaw('tipo, COUNT(*) as total')
                ->groupBy('tipo')
                ->get()
                ->pluck('total', 'tipo')
                ->toArray(),
            'por_modalidad' => $gestion->programas()
                ->selectRaw('modalidad, COUNT(*) as total')
                ->groupBy('modalidad')
                ->get()
                ->pluck('total', 'modalidad')
                ->toArray(),
        ];
    }

    /**
     * Obtener estadísticas de certificaciones de una gestión
     * 
     * @param Gestion $gestion
     * @return array
     */
    private function obtenerEstadisticasCertificaciones(Gestion $gestion): array
    {
        $certificaciones = $gestion->programas()->withCount('certificaciones')->get();
        
        return [
            'total' => $certificaciones->sum('certificaciones_count'),
            'por_programa' => $certificaciones->mapWithKeys(function ($programa) {
                return [$programa->nombre_carrera => $programa->certificaciones_count];
            })->toArray(),
        ];
    }

    /**
     * Obtener estadísticas de tesis de una gestión
     * 
     * @param Gestion $gestion
     * @return array
     */
    private function obtenerEstadisticasTesis(Gestion $gestion): array
    {
        $programas = $gestion->programas()->withCount('tesis')->get();
        
        return [
            'total' => $programas->sum('tesis_count'),
            'por_programa' => $programas->mapWithKeys(function ($programa) {
                return [$programa->nombre_carrera => $programa->tesis_count];
            })->toArray(),
        ];
    }
} 