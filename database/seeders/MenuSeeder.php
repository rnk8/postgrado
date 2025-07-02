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

        // Crear menús mapeados a sus permisos correspondientes
        $menus = [
            [
                'titulo' => 'Dashboard',
                'ruta' => 'dashboard',
                'icono' => 'heroicons-outline:home',
                'permiso' => 'ver_dashboard',
                'orden' => 1,
            ],
            [
                'titulo' => 'Gestiones',
                'ruta' => 'gestiones.index',
                'icono' => 'heroicons-outline:calendar',
                'permiso' => 'ver_gestiones',
                'orden' => 2,
            ],
            [
                'titulo' => 'Datos Académicos',
                'ruta' => 'datos-academicos.index',
                'icono' => 'heroicons-outline:document-data',
                'permiso' => 'ver_datos_academicos',
                'orden' => 3,
            ],
            [
                'titulo' => 'Carga Excel',
                'ruta' => 'excel.index',
                'icono' => 'heroicons-outline:upload',
                'permiso' => 'cargar_excel',
                'orden' => 4,
            ],
            [
                'titulo' => 'Docentes',
                'ruta' => 'docentes.index',
                'icono' => 'heroicons-outline:users',
                'permiso' => 'ver_docentes',
                'orden' => 5,
            ],
            [
                'titulo' => 'Programas',
                'ruta' => 'programas.index',
                'icono' => 'heroicons-outline:collection',
                'permiso' => 'ver_programas',
                'orden' => 6,
            ],
            [
                'titulo' => 'Certificaciones',
                'ruta' => 'certificaciones.index',
                'icono' => 'heroicons-outline:academic-cap',
                'permiso' => 'ver_certificaciones',
                'orden' => 7,
            ],
            [
                'titulo' => 'Tesis',
                'ruta' => 'tesis.index',
                'icono' => 'heroicons-outline:book-open',
                'permiso' => 'ver_tesis',
                'orden' => 8,
            ],
            [
                'titulo' => 'Usuarios',
                'ruta' => 'users.index',
                'icono' => 'heroicons-outline:user-group',
                'permiso' => 'ver_usuarios',
                'orden' => 9,
            ],
            [
                'titulo' => 'Reportes',
                'ruta' => 'reportes.index',
                'icono' => 'heroicons-outline:chart-bar',
                'permiso' => 'ver_reportes',
                'orden' => 10,
            ],
        ];

        foreach ($menus as $item) {
            Menu::create([
                'titulo' => $item['titulo'],
                'ruta' => $item['ruta'],
                'icono' => $item['icono'] ?? null,
                'permiso' => $item['permiso'],
                'orden' => $item['orden'],
                'activo' => true
            ]);
        }

        // El acceso se controla mediante el campo 'permiso' y los roles de Spatie,
        // por lo que no es necesario asignar roles directamente al menú aquí.
    }
} 