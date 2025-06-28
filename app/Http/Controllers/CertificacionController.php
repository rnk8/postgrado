<?php

namespace App\Http\Controllers;

use App\Models\Certificacion;
use App\Models\Programa;
use App\Services\GestionService;
use App\Http\Requests\StoreCertificacionRequest;
use App\Http\Requests\UpdateCertificacionRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Cache;

class CertificacionController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(private GestionService $gestionService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('ver_certificaciones');

        $gestionActual = Cache::get('gestion_actual');
        $query = Certificacion::with(['programa', 'gestion']);
        
        if ($gestionActual) {
            $query->where('gestion_id', $gestionActual->id);
        }

        if ($busqueda = $request->get('search')) {
            $query->where('nombre_est', 'like', "%{$busqueda}%")
                  ->orWhere('nro_registro_est', 'like', "%{$busqueda}%")
                  ->orWhere('numero', 'like', "%{$busqueda}%");
        }

        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }

        if ($programa = $request->get('programa_id')) {
            $query->where('programa_id', $programa);
        }

        $certificaciones = $query->latest()->paginate(15)->withQueryString();
        
        $programas = $gestionActual 
            ? Programa::where('gestion_id', $gestionActual->id)->select('id', 'nombre_carrera')->get()
            : collect();

        return Inertia::render('Certificaciones/Index', [
            'certificaciones' => $certificaciones,
            'programas' => $programas,
            'filters' => [
                'search' => $busqueda,
                'estado' => $estado,
                'programa_id' => $programa,
            ],
            'estadosDisponibles' => [
                'pendiente' => 'Pendiente',
                'emitido' => 'Emitido',
                'entregado' => 'Entregado',
            ],
            'permisos' => [
                'puede_crear' => $request->user()->can('crear_certificaciones'),
                'puede_editar' => $request->user()->can('editar_certificaciones'),
                'puede_eliminar' => $request->user()->can('eliminar_certificaciones'),
                'puede_emitir' => $request->user()->can('emitir_certificaciones'),
            ]
        ]);
    }

    public function create()
    {
        $this->authorize('crear_certificaciones');
        
        $gestionActual = Cache::get('gestion_actual');
        $programas = $gestionActual
            ? Programa::where('gestion_id', $gestionActual->id)->select('id', 'nombre_carrera')->get()
            : collect();
        
        return Inertia::render('Certificaciones/Create', [
            'programas' => $programas,
            'gestionActiva' => (bool)$gestionActual,
            'estadosDisponibles' => [
                'pendiente' => 'Pendiente',
                'emitido' => 'Emitido',
                'entregado' => 'Entregado',
            ],
        ]);
    }

    public function store(StoreCertificacionRequest $request)
    {
        $gestionActual = Cache::get('gestion_actual');
        if (!$gestionActual) {
            return back()->with('error', 'No hay una gestión académica activa. No se puede crear la certificación.');
        }
        
        // Generar número automático
        $year = now()->year;
        $count = Certificacion::whereYear('created_at', $year)->count() + 1;
        $numero = "CERT-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
        
        $certificacion = Certificacion::create([
            ...$request->validated(),
            'numero' => $numero,
            'gestion_id' => $gestionActual->id
        ]);
        
        $certificacion->calcularPromedio();
        
        return redirect()->route('certificaciones.index')
            ->with('success', 'Certificación creada correctamente');
    }

    public function show(Certificacion $certificacion)
    {
        $this->authorize('ver_certificaciones');
        
        $certificacion->load(['programa', 'gestion']);
        
        return Inertia::render('Certificaciones/Show', [
            'certificacion' => $certificacion,
        ]);
    }

    public function edit(Certificacion $certificacion)
    {
        $this->authorize('editar_certificaciones');
        
        $gestionActual = Cache::get('gestion_actual');
        $programas = $gestionActual
            ? Programa::where('gestion_id', $gestionActual->id)->select('id', 'nombre_carrera')->get()
            : collect([$certificacion->programa])->filter();
        
        return Inertia::render('Certificaciones/Edit', [
            'certificacion' => $certificacion,
            'programas' => $programas,
            'gestionActiva' => (bool)$gestionActual,
            'estadosDisponibles' => [
                'pendiente' => 'Pendiente',
                'emitido' => 'Emitido',
                'entregado' => 'Entregado',
            ],
        ]);
    }

    public function update(UpdateCertificacionRequest $request, Certificacion $certificacion)
    {
        $certificacion->update($request->validated());
        $certificacion->calcularPromedio();
        
        return redirect()->route('certificaciones.index')
            ->with('success', 'Certificación actualizada correctamente');
    }

    public function destroy(Certificacion $certificacion)
    {
        $this->authorize('eliminar_certificaciones');
        
        $certificacion->delete();
        
        return back()->with('success', 'Certificación eliminada correctamente');
    }

    public function emitir(Certificacion $certificacion)
    {
        $this->authorize('emitir_certificaciones');
        
        $certificacion->update([
            'estado' => 'emitido',
            'fecha_emision' => now(),
        ]);
        
        return back()->with('success', 'Certificación emitida correctamente');
    }
} 