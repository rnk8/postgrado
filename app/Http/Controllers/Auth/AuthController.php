<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use App\Models\User;

/**
 * Controlador de Autenticación
 * Maneja el login, logout y registro de usuarios del sistema
 */
class AuthController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Mostrar formulario de login
     * 
     * @return \Inertia\Response
     */
    public function showLogin()
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => true,
            'status' => session('status'),
        ]);
    }

    /**
     * Procesar login del usuario
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validación de campos requeridos
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Intentar autenticar al usuario
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', '¡Bienvenido al Sistema de Postgrado!');
        }

        // Si la autenticación falla
        throw ValidationException::withMessages([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Procesar logout del usuario
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Mostrar formulario de registro (solo para administradores)
     * 
     * @param Request $request
     * @return \Inertia\Response
     */
    public function showRegister(Request $request)
    {
        // Solo administradores pueden acceder al registro
        $this->authorize('crear_usuarios');

        return Inertia::render('Auth/Register', [
            'roles' => $this->obtenerRolesDisponibles(),
        ]);
    }

    /**
     * Procesar registro de nuevo usuario
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Solo administradores pueden crear usuarios
        $this->authorize('crear_usuarios');

        // Validación de datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:director,secretario',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'role.required' => 'Debe seleccionar un rol.',
            'role.in' => 'El rol seleccionado no es válido.',
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(), // Verificamos automáticamente para usuarios internos
        ]);

        // Asignar rol al usuario
        $user->assignRole($validated['role']);

        return redirect()->route('dashboard')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Mostrar perfil del usuario autenticado
     * 
     * @param Request $request
     * @return \Inertia\Response
     */
    public function showProfile(Request $request)
    {
        return Inertia::render('Auth/Profile', [
            'user' => $request->user()->load('roles'),
            'sessions' => $this->obtenerSesionesActivas($request),
        ]);
    }

    /**
     * Actualizar perfil del usuario
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        // Actualizar datos básicos
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Actualizar contraseña si se proporcionó
        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Obtener roles disponibles para asignación
     * 
     * @return array
     */
    private function obtenerRolesDisponibles(): array
    {
        return [
            ['value' => 'director', 'label' => 'Director de Postgrado'],
            ['value' => 'secretario', 'label' => 'Secretario Académico'],
        ];
    }

    /**
     * Obtener sesiones activas del usuario (simulado)
     * 
     * @param Request $request
     * @return array
     */
    private function obtenerSesionesActivas(Request $request): array
    {
        return [
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_activity' => now(),
                'is_current' => true,
            ]
        ];
    }
} 