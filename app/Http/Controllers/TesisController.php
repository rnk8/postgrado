<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use App\Models\Programa;
use App\Models\Docente;
use App\Services\GestionService;
use App\Http\Requests\StoreTesisRequest;
use App\Http\Requests\UpdateTesisRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class TesisController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(private GestionService $gestionService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('ver_tesis');

        $query = Tesis::with(['programa', 'tutor', 'gestion']);
        
        if ($busqueda = $request->get('search')) {
            $query->where('titulo', 'like', "%{$busqueda}%")
                  ->orWhere('nombre_est', 'like', "%{$busqueda}%")
                  ->orWhere('nro_registro_est', 'like', "%{$busqueda}%")
                  ->orWhere('codigo', 'like', "%{$busqueda}%");
        }

        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }

        if ($programa = $request->get('programa_id')) {
            $query->where('programa_id', $programa);
        }

        if ($tutor = $request->get('tutor_id')) {
            $query->where('tutor_id', $tutor);
        }

        $tesis = $query->latest()->paginate(15)->withQueryString();
        $programas = Programa::select('id', 'nombre_carrera')->get();
        $tutores = Docente::select('id', 'nombre_doc')->get();

        return Inertia::render('Tesis/Index', [
            'tesis' => $tesis,
            'programas' => $programas,
            'tutores' => $tutores,
            'filters' => [
                'search' => $busqueda,
                'estado' => $estado,
                'programa_id' => $programa,
                'tutor_id' => $tutor,
            ],
            'estadosDisponibles' => [
                'en_desarrollo' => 'En Desarrollo',
                'defendida' => 'Defendida',
                'aprobada' => 'Aprobada',
                'rechazada' => 'Rechazada',
            ],
            'permisos' => [
                'puede_crear' => $request->user()->can('crear_tesis'),
                'puede_editar' => $request->user()->can('editar_tesis'),
                'puede_eliminar' => $request->user()->can('eliminar_tesis'),
                'puede_aprobar' => $request->user()->can('aprobar_tesis'),
            ]
        ]);
    }

    public function create()
    {
        $this->authorize('crear_tesis');
        
        $programas = Programa::select('id', 'nombre_carrera')->get();
        $tutores = Docente::select('id', 'nombre_doc')->get();
        
        return Inertia::render('Tesis/Create', [
            'programas' => $programas,
            'tutores' => $tutores,
            'estadosDisponibles' => [
                'en_desarrollo' => 'En Desarrollo',
                'defendida' => 'Defendida',
                'aprobada' => 'Aprobada',
                'rechazada' => 'Rechazada',
            ],
        ]);
    }

    public function store(StoreTesisRequest $request)
    {
        $gestionActual = $this->gestionService->obtenerGestionActual();
        
        // Generar código automático
        $year = now()->year;
        $count = Tesis::whereYear('created_at', $year)->count() + 1;
        $codigo = "TESIS-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
        
        Tesis::create([
            ...$request->validated(),
            'codigo' => $codigo,
            'gestion_id' => $gestionActual?->id
        ]);
        
        return redirect()->route('tesis.index')
            ->with('success', 'Tesis creada correctamente');
    }

    public function show(Tesis $tesis)
    {
        $this->authorize('ver_tesis');
        
        $tesis->load(['programa', 'tutor', 'gestion']);
        
        return Inertia::render('Tesis/Show', [
            'tesis' => $tesis,
        ]);
    }

    public function edit(Tesis $tesis)
    {
        $this->authorize('editar_tesis');
        
        $programas = Programa::select('id', 'nombre_carrera')->get();
        $tutores = Docente::select('id', 'nombre_doc')->get();
        
        return Inertia::render('Tesis/Edit', [
            'tesis' => $tesis,
            'programas' => $programas,
            'tutores' => $tutores,
            'estadosDisponibles' => [
                'en_desarrollo' => 'En Desarrollo',
                'defendida' => 'Defendida',
                'aprobada' => 'Aprobada',
                'rechazada' => 'Rechazada',
            ],
        ]);
    }

    public function update(UpdateTesisRequest $request, Tesis $tesis)
    {
        $tesis->update($request->validated());
        
        return redirect()->route('tesis.index')
            ->with('success', 'Tesis actualizada correctamente');
    }

    public function destroy(Tesis $tesis)
    {
        $this->authorize('eliminar_tesis');
        
        $tesis->delete();
        
        return back()->with('success', 'Tesis eliminada correctamente');
    }

    public function aprobar(Tesis $tesis)
    {
        $this->authorize('aprobar_tesis');
        
        $tesis->update(['estado' => 'aprobada']);
        
        return back()->with('success', 'Tesis aprobada correctamente');
    }
} 