<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Docente;
use App\Models\Programa;
use App\Models\Certificacion;
use App\Models\Tesis;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Cache;

class SearchController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Búsqueda global en múltiples modelos.
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'q' => 'nullable|string|min:2',
            'suggest' => 'nullable|boolean',
        ]);

        $query = $request->input('q', '');
        $isSuggestion = $request->boolean('suggest');
        $gestionActual = Cache::get('gestion_actual');

        // Si no hay término de búsqueda, retornar resultados vacíos
        $results = [
            'docentes' => collect(),
            'programas' => collect(),
            'certificaciones' => collect(),
            'tesis' => collect(),
            'usuarios' => collect(),
        ];

        if ($query !== '') {
            // Limitar resultados para sugerencias
            $limit = $isSuggestion ? 3 : 10;
            
            // Construir consultas base con filtro de gestión si aplica
            $docentesQuery = $gestionActual ? Docente::where('gestion_id', $gestionActual->id) : Docente::query();
            $programasQuery = $gestionActual ? Programa::where('gestion_id', $gestionActual->id) : Programa::query();
            $certificacionesQuery = $gestionActual ? Certificacion::where('gestion_id', $gestionActual->id) : Certificacion::query();
            $tesisQuery = $gestionActual ? Tesis::where('gestion_id', $gestionActual->id) : Tesis::query();

            $results = [
                'docentes' => $docentesQuery
                    ->select('id', 'nombre_doc', 'cod_doc')
                    ->where(function ($q) use ($query) {
                        $q->where('nombre_doc', 'like', "%{$query}%")
                          ->orWhere('cod_doc', 'like', "%{$query}%");
                    })
                    ->limit($limit)
                    ->get(),
                'programas' => $programasQuery
                    ->select('id', 'nombre_carrera', 'cod_carrera')
                    ->where(function ($q) use ($query) {
                        $q->where('nombre_carrera', 'like', "%{$query}%")
                          ->orWhere('cod_carrera', 'like', "%{$query}%");
                    })
                    ->limit($limit)
                    ->get(),
                'certificaciones' => $certificacionesQuery
                    ->select('id', 'numero', 'nombre_est', 'nro_registro_est')
                    ->where(function ($q) use ($query) {
                        $q->where('numero', 'like', "%{$query}%")
                          ->orWhere('nombre_est', 'like', "%{$query}%")
                          ->orWhere('nro_registro_est', 'like', "%{$query}%");
                    })
                    ->limit($limit)
                    ->get(),
                'tesis' => $tesisQuery
                    ->select('id', 'titulo', 'codigo', 'nombre_est')
                    ->where(function ($q) use ($query) {
                        $q->where('titulo', 'like', "%{$query}%")
                          ->orWhere('codigo', 'like', "%{$query}%")
                          ->orWhere('nombre_est', 'like', "%{$query}%");
                    })
                    ->limit($limit)
                    ->get(),
                'usuarios' => User::select('id', 'name', 'email')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%");
                    })
                    ->limit($limit)
                    ->get(),
            ];
        }

        // Si la petición espera JSON (por ejemplo, llamadas AJAX/autocompletado), retornamos JSON
        if ($request->wantsJson()) {
            return response()->json($results);
        }

        // De lo contrario, renderizamos la página de resultados con Inertia
        return \Inertia\Inertia::render('Search/Results', [
            'query' => $query,
            'results' => $results,
        ]);
    }
} 