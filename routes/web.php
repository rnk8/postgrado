<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TesisController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Rutas Web del Sistema de Postgrado
|--------------------------------------------------------------------------
| Aquí se definen todas las rutas web de la aplicación. Estas rutas son
| cargadas por el RouteServiceProvider y todas tienen el middleware 'web'.
|
*/

// Redirección de la raíz al dashboard o login
Route::get('/', function () {
    return Auth::check() 
        ? redirect()->route('dashboard') 
        : redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    // Registro (solo para administradores cuando estén autenticados)
});

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Perfil de usuario
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    // Registro de usuarios (solo administradores)
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register')
        ->middleware('can:crear_usuarios');
    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.post')
        ->middleware('can:crear_usuarios');
});

/*
|--------------------------------------------------------------------------
| Rutas Principales del Sistema
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestiones académicas
    Route::resource('gestiones', GestionController::class)->parameters(['gestiones' => 'gestion']);
    Route::put('/gestiones/{gestion}/activar', [GestionController::class, 'activar'])
        ->name('gestiones.activar')
        ->middleware('can:activar_gestiones');
    
    // Módulos principales del sistema
    Route::resource('docentes', App\Http\Controllers\DocenteController::class)->parameters(['docentes'=>'docente']);
    Route::resource('programas', App\Http\Controllers\ProgramaController::class)->parameters(['programas'=>'programa']);
    
    // Certificaciones
    Route::resource('certificaciones', App\Http\Controllers\CertificacionController::class);
    Route::put('/certificaciones/{certificacion}/emitir', [App\Http\Controllers\CertificacionController::class, 'emitir'])
        ->name('certificaciones.emitir')
        ->middleware('can:emitir_certificaciones');
    
    // Tesis
    Route::resource('tesis', TesisController::class);
    Route::put('tesis/{tesis}/aprobar', [TesisController::class, 'aprobar'])->name('tesis.aprobar');
    
    // Gestión de Usuarios y Roles
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

    // Buscador global
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    
    // Carga Excel
    Route::get('/excel', [App\Http\Controllers\ExcelController::class, 'index'])->name('excel.index');
    Route::post('/excel/upload', [App\Http\Controllers\ExcelController::class, 'upload'])->name('excel.upload');
    Route::post('/excel/{carga}/procesar', [App\Http\Controllers\ExcelController::class, 'procesar'])->name('excel.procesar');
    Route::get('/excel/{carga}', [App\Http\Controllers\ExcelController::class, 'show'])->name('excel.show');
    Route::delete('/excel/{carga}', [App\Http\Controllers\ExcelController::class, 'destroy'])->name('excel.destroy');
    Route::get('/excel/plantilla/descargar', [App\Http\Controllers\ExcelController::class, 'descargarPlantilla'])->name('excel.plantilla');
    
    // Estadísticas en tiempo real
    Route::get('/stats/dashboard', function () {
        // TODO: Implementar estadísticas del dashboard
    });

    // Menú dinámico
    Route::get('/menu', [\App\Http\Controllers\MenuController::class, 'index']);
});

// Rutas de Reportes
Route::middleware(['auth', 'can:ver_reportes'])->prefix('reportes')->name('reportes.')->group(function () {
    Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('index');
    Route::get('/informe-anual', [App\Http\Controllers\ReportController::class, 'informeAnual'])->name('informeAnual');
    Route::get('/resumen-programas', [App\Http\Controllers\ReportController::class, 'resumenProgramas'])->name('resumenProgramas');
    Route::get('/estado-alumnos', [App\Http\Controllers\ReportController::class, 'estadoAlumnos'])->name('estadoAlumnos');
    Route::get('/reporte-docentes', [App\Http\Controllers\ReportController::class, 'reporteDocentes'])->name('reporteDocentes');
    Route::get('/resumen-defensas', [App\Http\Controllers\ReportController::class, 'resumenDefensas'])->name('resumenDefensas');
    Route::get('/data', [App\Http\Controllers\ReportController::class, 'getData'])->name('data');
});

/*
|--------------------------------------------------------------------------
| Rutas de API (para funcionalidades AJAX)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->middleware(['auth'])->group(function () {
    // Búsqueda global con sugerencias
    Route::get('/search', [SearchController::class, 'index']);
    
    // Búsquedas específicas
    Route::get('/search/docentes', function () {
        // TODO: Implementar búsqueda específica de docentes
    });
    
    Route::get('/search/programas', function () {
        // TODO: Implementar búsqueda específica de programas
    });
    
    // Estadísticas en tiempo real
    Route::get('/stats/dashboard', function () {
        // TODO: Implementar estadísticas del dashboard
    });

    // Menú dinámico
    Route::get('/menu', [\App\Http\Controllers\MenuController::class, 'index']);
});

Route::get('/busqueda', [SearchController::class, 'index'])->name('busqueda');
