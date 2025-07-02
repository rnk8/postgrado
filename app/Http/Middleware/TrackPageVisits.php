<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackPageVisits
{
    /**
     * Manejar una petición entrante.
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo contar peticiones GET a rutas web, no API ni assets estáticos
        if ($request->isMethod('get') && 
            !$request->is('api/*') && 
            !$request->is('storage/*') &&
            !$request->is('images/*') &&
            !$request->is('css/*') &&
            !$request->is('js/*') &&
            !$request->is('favicon.ico') &&
            !$request->is('robots.txt')) {
            
            $routeName = $request->route()?->getName() ?: $request->path();

            // Upsert para incrementar o crear el contador
            DB::table('page_visits')->upsert([
                'route' => $routeName,
                'visitas' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ], ['route'], [
                'visitas' => DB::raw('page_visits.visitas + 1'),
                'updated_at' => now(),
            ]);

            // Obtener el nuevo contador actualizado
            $count = DB::table('page_visits')->where('route', $routeName)->value('visitas') ?? 0;
            $request->attributes->set('visitas_pagina', $count);
        }

        return $next($request);
    }
} 