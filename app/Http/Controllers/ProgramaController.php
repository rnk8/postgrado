<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\Docente;
use App\Services\GestionService;
use App\Http\Requests\StoreProgramaRequest;
use App\Http\Requests\UpdateProgramaRequest;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgramaController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(private GestionService $gestionService){}

    public function index(Request $request)
    {
        $this->authorize('ver_programas');
        $query = Programa::with('coordinador');
        if ($buscar = $request->get('search')) {
            $query->where('nombre_carrera', 'like', "%{$buscar}%")
                  ->orWhere('cod_carrera', 'like', "%{$buscar}%");
        }
        $programas = $query->latest()->paginate(10)->withQueryString();
        return Inertia::render('Programas/Index', [
            'programas' => $programas,
            'filters' => ['search' => $buscar],
            'permisos' => [
                'puede_crear' => $request->user()->can('crear_programas'),
                'puede_editar' => $request->user()->can('editar_programas'),
                'puede_eliminar' => $request->user()->can('eliminar_programas'),
            ]
        ]);
    }

    public function create()
    {
        $this->authorize('crear_programas');
        $docentes = Docente::where('estado', 'activo')->orderBy('nombre_doc')->get(['id', 'nombre_doc']);
        return Inertia::render('Programas/Create', [
            'docentes' => $docentes,
        ]);
    }

    public function store(StoreProgramaRequest $request)
    {
        $gestion = $this->gestionService->obtenerGestionActual();
        Programa::create([...$request->validated(), 'gestion_id' => $gestion?->id]);
        return redirect()->route('programas.index')->with('success','Programa creado');
    }

    public function edit(Programa $programa)
    {
        $this->authorize('editar_programas');
        $docentes = Docente::where('estado', 'activo')->orderBy('nombre_doc')->get(['id', 'nombre_doc']);
        return Inertia::render('Programas/Edit',[
            'programa' => $programa,
            'docentes' => $docentes,
        ]);
    }

    public function update(UpdateProgramaRequest $request, Programa $programa)
    {
        $programa->update($request->validated());
        return redirect()->route('programas.index')->with('success','Programa actualizado');
    }

    /**
     * Mostrar detalles de un programa
     */
    public function show(Request $request, Programa $programa)
    {
        $this->authorize('ver_programas');
        $programa->load(['coordinador', 'gestion']);
        return Inertia::render('Programas/Show', [
            'programa' => $programa,
            'permisos' => [
                'puede_editar' => $request->user()->can('editar_programas'),
                'puede_eliminar' => $request->user()->can('eliminar_programas'),
            ],
        ]);
    }

    public function destroy(Programa $programa)
    {
        $this->authorize('eliminar_programas');
        $programa->delete();
        return back()->with('success','Programa eliminado');
    }
} 