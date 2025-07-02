<?php

namespace App\Http\Controllers;

use App\Models\CargaExcel;
use App\Models\DatoAcademico;
use App\Services\GestionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DatosAcademicosImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ExcelController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(private GestionService $gestionService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('cargar_excel');
        
        $gestionActual = Cache::get('gestion_actual');
        $query = CargaExcel::with(['user', 'gestion']);

        if ($gestionActual) {
            $query->where('gestion_id', $gestionActual->id);
        }
        
        if ($busqueda = $request->get('search')) {
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre_archivo', 'like', "%{$busqueda}%")
                  ->orWhere('descripcion', 'like', "%{$busqueda}%");
            });
        }

        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }

        $cargas = $query->latest()->paginate(15)->withQueryString();

        // Estadísticas contextuales a la gestión actual si existe
        $statsQuery = $gestionActual ? CargaExcel::where('gestion_id', $gestionActual->id) : CargaExcel::query();
        
        $estadisticas = [
            'total_cargas' => $statsQuery->clone()->count(),
            'cargas_exitosas' => $statsQuery->clone()->where('estado', 'completado')->count(),
            'cargas_error' => $statsQuery->clone()->where('estado', 'error')->count(),
            'registros_procesados' => $statsQuery->clone()->sum('registros_procesados'),
        ];

        return Inertia::render('Excel/Index', [
            'cargas' => $cargas,
            'estadisticas' => $estadisticas,
            'filters' => [
                'search' => $busqueda,
                'estado' => $estado,
            ],
            'estadosDisponibles' => [
                'pendiente' => 'Pendiente',
                'procesando' => 'Procesando',
                'completado' => 'Completado',
                'error' => 'Error',
            ],
            'permisos' => [
                'puede_cargar' => $request->user()->can('cargar_excel'),
                'puede_eliminar' => $request->user()->can('eliminar_cargas_excel'),
                'puede_reprocesar' => $request->user()->can('reprocesar_excel'),
            ]
        ]);
    }

    public function upload(Request $request)
    {
        $this->authorize('cargar_excel');

        $gestionActual = Cache::get('gestion_actual');
        if (!$gestionActual) {
            return back()->with('error', 'No hay una gestión académica activa. No se puede subir el archivo.');
        }

        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
            'descripcion' => 'nullable|string|max:500',
        ], [
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.file' => 'El archivo no es válido.',
            'archivo.mimes' => 'El archivo debe ser de tipo Excel (.xlsx, .xls) o CSV.',
            'archivo.max' => 'El archivo no puede ser mayor a 10MB.',
        ]);

        try {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            
            // Guardar archivo en storage
            $rutaArchivo = $archivo->storeAs('excel_uploads', $nombreArchivo, 'local');
            
            // Crear registro de carga
            $carga = CargaExcel::create([
                'nombre_archivo' => $archivo->getClientOriginalName(),
                'ruta_archivo' => $rutaArchivo,
                'descripcion' => $request->input('descripcion'),
                'estado' => 'pendiente',
                'user_id' => $request->user()->id,
                'gestion_id' => $gestionActual->id,
            ]);

            return redirect()->route('excel.index')
                ->with('success', 'Archivo subido correctamente. Procesando...');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al subir el archivo: ' . $e->getMessage());
        }
    }

    public function procesar(CargaExcel $carga)
    {
        $this->authorize('cargar_excel');

        try {
            $carga->marcarComoProcesando();

            // Verificar que el archivo existe
            if (!Storage::disk('local')->exists($carga->ruta_archivo)) {
                throw new \Exception('El archivo no existe en el sistema.');
            }

            $rutaCompleta = Storage::disk('local')->path($carga->ruta_archivo);
            
            // Procesar el archivo Excel
            $import = new DatosAcademicosImport($carga);
            Excel::import($import, $rutaCompleta);

            // Obtener estadísticas del procesamiento
            $registrosProcesados = $import->getRegistrosProcesados();
            $registrosExitosos = $import->getRegistrosExitosos();
            $registrosConError = $import->getRegistrosConError();
            $errores = $import->getErrores();

            // Actualizar contadores
            $carga->actualizarContadores($registrosProcesados, $registrosExitosos, $registrosConError);

            // Marcar como completado con resumen
            $resumen = [
                'registros_procesados' => $registrosProcesados,
                'registros_exitosos' => $registrosExitosos,
                'registros_con_error' => $registrosConError,
                'errores' => $errores,
                'fecha_procesamiento' => now()->format('Y-m-d H:i:s'),
            ];

            $carga->marcarComoCompletado($resumen);

            return back()->with('success', "Archivo procesado correctamente. {$registrosExitosos} registros importados.");

        } catch (\Exception $e) {
            $carga->marcarComoError($e->getMessage());
            
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    public function show(Request $request, CargaExcel $carga)
    {
        $this->authorize('cargar_excel');

        $carga->load(['user', 'gestion']);

        // Obtener datos académicos relacionados si existen
        $datosAcademicos = DatoAcademico::where('carga_excel_id', $carga->id)
            ->with(['gestion'])
            ->paginate(20);

        // Estadísticas específicas de esta carga
        $estadisticasCarga = [
            'total_estudiantes' => DatoAcademico::where('carga_excel_id', $carga->id)
                ->distinct('nro_registro_est')->count(),
            'estudiantes_con_defensa' => DatoAcademico::where('carga_excel_id', $carga->id)
                ->whereNotNull('fecha_defensa_tfg')->distinct('nro_registro_est')->count(),
            'programas_diferentes' => DatoAcademico::where('carga_excel_id', $carga->id)
                ->distinct('cod_carrera')->count(),
            'docentes_diferentes' => DatoAcademico::where('carga_excel_id', $carga->id)
                ->whereNotNull('cod_doc')->distinct('cod_doc')->count(),
        ];

        return Inertia::render('Excel/Show', [
            'carga' => $carga,
            'datosAcademicos' => $datosAcademicos,
            'estadisticasCarga' => $estadisticasCarga,
            'permisos' => [
                'puede_reprocesar' => $request->user()->can('reprocesar_excel'),
                'puede_eliminar' => $request->user()->can('eliminar_cargas_excel'),
            ]
        ]);
    }

    public function destroy(CargaExcel $carga)
    {
        $this->authorize('eliminar_cargas_excel');

        try {
            // Eliminar archivo físico si existe
            if (Storage::disk('local')->exists($carga->ruta_archivo)) {
                Storage::disk('local')->delete($carga->ruta_archivo);
            }

            // Eliminar datos académicos relacionados
            DatoAcademico::where('carga_excel_id', $carga->id)->delete();

            // Eliminar registro de carga
            $carga->delete();

            return back()->with('success', 'Carga eliminada correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la carga: ' . $e->getMessage());
        }
    }

    public function descargarPlantilla()
    {
        $this->authorize('cargar_excel');

        // Crear archivo de plantilla con headers
        $headers = [
            'A1' => 'nro_registro_est',
            'B1' => 'nombre_est',
            'C1' => 'genero_est',
            'D1' => 'cod_carrera',
            'E1' => 'nombre_carrera',
            'F1' => 'cod_doc',
            'G1' => 'nombre_doc',
            'H1' => 'nota',
            'I1' => 'nota_defensa_tfg',
            'J1' => 'fecha_defensa_tfg',
        ];

        return response()->streamDownload(function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array_values($headers));
            fclose($file);
        }, 'plantilla_datos_academicos.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
} 