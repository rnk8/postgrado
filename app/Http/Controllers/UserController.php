<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class UserController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Muestra la lista de usuarios con sus roles.
     */
    public function index(Request $request)
    {
        $this->authorize('ver_usuarios');

        $users = User::with('roles')->paginate(10);
        $roles = Role::all()->pluck('name');
        
        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'permisos' => [
                'puede_editar_roles' => $request->user()->can('editar_roles_usuario'),
                'puede_crear' => $request->user()->can('crear_usuarios'),
                'puede_eliminar' => $request->user()->can('eliminar_usuarios'),
                'puede_editar' => $request->user()->can('editar_usuarios'),
            ]
        ]);
    }

    /**
     * Actualiza los roles de un usuario.
     */
    public function update(Request $request, User $user)
    {
        // Autorizar: el usuario debe poder editar usuarios o roles
        if (!$request->user()->can('editar_usuarios') && !$request->user()->can('editar_roles_usuario')) {
            abort(403);
        }

        $data = $request->validate([
            'name'   => ['sometimes', 'required', 'string', 'max:255'],
            'email'  => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'roles'  => ['sometimes', 'array'],
            'roles.*'=> ['string', 'exists:roles,name'],
        ]);

        // Actualizar datos básicos si vienen en la solicitud
        if (isset($data['name']) || isset($data['email'])) {
            $user->update(array_filter($data, fn($key) => in_array($key, ['name','email']), ARRAY_FILTER_USE_KEY));
        }

        // Actualizar roles si se enviaron y el solicitante tiene permiso
        if (isset($data['roles']) && $request->user()->can('editar_roles_usuario')) {
            $user->syncRoles($data['roles']);
        }

        return back()->with('success', 'Usuario actualizado correctamente.');
    }
}
