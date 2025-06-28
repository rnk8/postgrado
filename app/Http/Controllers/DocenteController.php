<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Services\GestionService;
use App\Http\Requests\StoreDocenteRequest;
use App\Http\Requests\UpdateDocenteRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\Gestion;
use Illuminate\Support\Facades\Cache;

class DocenteController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(private GestionService $gestionService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('ver_docentes');

        $gestionActual = Cache::get('gestion_actual');
        $query = Docente::query();

        if ($gestionActual) {
            $query->where('gestion_id', $gestionActual->id);
        }

        if ($busqueda = $request->get('search')) {
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre_doc', 'like', "%{$busqueda}%")
                  ->orWhere('cod_doc', 'like', "%{$busqueda}%");
            });
        }

        $docentes = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Docentes/Index', [
            'docentes' => $docentes,
            'filters' => [ 'search' => $busqueda ],
            'permisos' => [
                'puede_crear' => $request->user()->can('crear_docentes'),
                'puede_editar' => $request->user()->can('editar_docentes'),
                'puede_eliminar' => $request->user()->can('eliminar_docentes'),
            ]
        ]);
    }

    public function create()
    {
        $this->authorize('crear_docentes');
        return Inertia::render('Docentes/Create');
    }

    public function store(StoreDocenteRequest $request)
    {
        $gestionActual = Cache::get('gestion_actual');
        if (!$gestionActual) {
            return back()->with('error', 'No hay una gestión académica activa. No se puede crear el docente.');
        }

        Docente::create([...$request->validated(), 'gestion_id' => $gestionActual->id]);

        return redirect()->route('docentes.index')->with('success', 'Docente creado correctamente.');
    }

    public function edit(Docente $docente)
    {
        $this->authorize('editar_docentes');
        return Inertia::render('Docentes/Edit', [ 'docente' => $docente ]);
    }

    public function update(UpdateDocenteRequest $request, Docente $docente)
    {
        $docente->update($request->validated());
        return redirect()->route('docentes.index')->with('success', 'Docente actualizado');
    }

    /**
     * Mostrar detalles de un docente
     */
    public function show(Request $request, Docente $docente)
    {
        $this->authorize('ver_docentes');
        $docente->load('gestion');
        return Inertia::render('Docentes/Show', [
            'docente' => $docente,
            'permisos' => [
                'puede_editar' => $request->user()->can('editar_docentes'),
                'puede_eliminar' => $request->user()->can('eliminar_docentes'),
            ],
        ]);
    }

    public function destroy(Docente $docente)
    {
        $this->authorize('eliminar_docentes');
        $docente->delete();
        return back()->with('success', 'Docente eliminado');
    }
} 