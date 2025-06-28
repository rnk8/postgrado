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
        // Solo contar peticiones GET a rutas web, no API ni assets
        if ($request->isMethod('get') && !$request->is('api/*') && !$request->ajax()) {
            $routeName = $request->route()?->getName() ?: $request->path();

            // Upsert
            DB::table('page_visits')->upsert([
                'route' => $routeName,
                'visitas' => 1,
                'updated_at' => now(),
            ], ['route'], [
                'visitas' => DB::raw('page_visits.visitas + 1'),
                'updated_at',
            ]);

            // Obtener el nuevo contador
            $count = DB::table('page_visits')->where('route', $routeName)->value('visitas');
            $request->attributes->set('visitas_pagina', $count);
        }

        return $next($request);
    }
} 