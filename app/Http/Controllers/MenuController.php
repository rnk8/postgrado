<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class MenuController extends BaseController
{
    /**
     * Devuelve el menú adaptado a los roles del usuario autenticado.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // IDs de roles del usuario (Spatie)
        $rolesIds = $user?->roles->pluck('id')->toArray() ?? [];

        // Cachear el menú por usuario para mejor rendimiento
        $cacheKey = 'user_menu_' . $user->id;
        $menu = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($rolesIds) {
            return Menu::with(['children' => function ($q) {
                $q->orderBy('orden');
            }])
                ->whereNull('parent_id')
                ->forRoles($rolesIds)
                ->orderBy('orden')
                ->get();
        });

        // Retornar JSON o Inertia según petición
        if ($request->wantsJson()) {
            return response()->json($menu);
        }

        return Inertia::render('Menu/Index', [
            'menu' => $menu,
        ]);
    }
} 