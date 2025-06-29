<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Menu;
use App\Models\Gestion;
use Illuminate\Support\Facades\Cache;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
 
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
 
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
 
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            
            // Información del usuario autenticado
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'roles' => $request->user()->getRoleNames(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                ] : null,
            ],
            
            // Mensajes flash del sistema
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
            
            // Configuraciones globales del sistema
            'app' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
                'timezone' => config('app.timezone'),
                'locale' => app()->getLocale(),
            ],
            
            // Información del sistema postgrado
            'system' => [
                'universidad' => 'Universidad Autónoma Gabriel René Moreno',
                'facultad' => 'Facultad de Ciencias Exactas y Tecnología',
                'direccion' => 'Dirección de Postgrado',
                'version' => '1.0.0',
                'gestion_actual' => function () {
                    return Cache::remember('gestion_actual', now()->addMinutes(60), function () {
                        return Gestion::where('es_actual', true)->first();
                    });
                },
            ],
            
            // Menú dinámico filtrado por permisos (simple y optimizado)
            'menu' => function () use ($request) {
                if (!$request->user()) {
                    return [];
                }

                return Menu::whereNull('parent_id')
                    ->orderBy('orden')
                    ->get()
                    ->filter(function ($item) use ($request) {
                        // Solo mostrar si el usuario tiene el permiso o si no requiere permiso
                        return !$item->permiso || $request->user()->can($item->permiso);
                    })
                    ->values();
            },
            
            'visitas_pagina' => $request->attributes->get('visitas_pagina'),
        ];
    }
}

