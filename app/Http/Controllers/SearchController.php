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
        ]);

        $query = $request->input('q', '');

        // Si no hay término de búsqueda, retornar resultados vacíos
        $results = [
            'docentes' => collect(),
            'programas' => collect(),
            'certificaciones' => collect(),
            'tesis' => collect(),
            'usuarios' => collect(),
        ];

        if ($query !== '') {
            $results = [
                'docentes' => Docente::select('id', 'nombre_doc', 'cod_doc')
                    ->where('nombre_doc', 'like', "%{$query}%")
                    ->orWhere('cod_doc', 'like', "%{$query}%")
                    ->limit(10)
                    ->get(),
                'programas' => Programa::select('id', 'nombre_carrera', 'cod_carrera')
                    ->where('nombre_carrera', 'like', "%{$query}%")
                    ->orWhere('cod_carrera', 'like', "%{$query}%")
                    ->limit(10)
                    ->get(),
                'certificaciones' => Certificacion::select('id', 'numero', 'nombre_est', 'nro_registro_est')
                    ->where('numero', 'like', "%{$query}%")
                    ->orWhere('nombre_est', 'like', "%{$query}%")
                    ->orWhere('nro_registro_est', 'like', "%{$query}%")
                    ->limit(10)
                    ->get(),
                'tesis' => Tesis::select('id', 'titulo', 'codigo', 'nombre_est')
                    ->where('titulo', 'like', "%{$query}%")
                    ->orWhere('codigo', 'like', "%{$query}%")
                    ->orWhere('nombre_est', 'like', "%{$query}%")
                    ->limit(10)
                    ->get(),
                'usuarios' => User::select('id', 'name', 'email')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%");
                    })
                    ->limit(10)
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