<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Spatie\Permission\Models\Role;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar la tabla de menús para evitar duplicados
        Menu::query()->delete();

        // Crear algunos menús de ejemplo
        $menus = [
            [
                'titulo' => 'Dashboard',
                'ruta' => 'dashboard',
                'icono' => 'heroicons-outline:home',
                'roles' => ['administrador', 'director', 'secretario'],
            ],
            [
                'titulo' => 'Gestiones',
                'ruta' => 'gestiones.index',
                'icono' => 'heroicons-outline:calendar',
                'roles' => ['administrador', 'director'],
            ],
            [
                'titulo' => 'Carga Excel',
                'ruta' => 'excel.index',
                'icono' => 'heroicons-outline:upload',
                'roles' => ['administrador', 'secretario'],
            ],
            [
                'titulo' => 'Docentes',
                'ruta' => 'docentes.index',
                'icono' => 'heroicons-outline:users',
                'roles' => ['administrador', 'director', 'secretario'],
            ],
            [
                'titulo' => 'Programas',
                'ruta' => 'programas.index',
                'icono' => 'heroicons-outline:collection',
                'roles' => ['administrador', 'director', 'secretario'],
            ],
            [
                'titulo' => 'Certificaciones',
                'ruta' => 'certificaciones.index',
                'icono' => 'heroicons-outline:academic-cap',
                'roles' => ['administrador', 'director', 'secretario'],
            ],
            [
                'titulo' => 'Tesis',
                'ruta' => 'tesis.index',
                'icono' => 'heroicons-outline:book-open',
                'roles' => ['administrador', 'director', 'secretario'],
            ],
            [
                'titulo' => 'Usuarios',
                'ruta' => 'users.index',
                'icono' => 'heroicons-outline:user-group',
                'roles' => ['administrador'],
            ],
        ];

        foreach ($menus as $item) {
            $menu = Menu::create([
                'titulo' => $item['titulo'],
                'ruta' => $item['ruta'],
                'icono' => $item['icono'],
                'orden' => 0,
            ]);
        }

        // Menú de Reportes
        Menu::create([
            'titulo' => 'Reportes',
            'ruta' => 'reportes.index',
            'orden' => 6,
            'permiso' => 'ver_reportes',
            'activo' => true
        ]);

        // El acceso se controla mediante el campo 'permiso' y los roles de Spatie,
        // por lo que no es necesario asignar roles directamente al menú aquí.
    }
} 