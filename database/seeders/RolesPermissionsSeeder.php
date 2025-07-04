<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // Usuarios
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'editar_roles_usuario',
            'eliminar_usuarios',
            
            // Gestiones
            'ver_gestiones',
            'crear_gestiones',
            'editar_gestiones',
            'eliminar_gestiones',
            'activar_gestiones',
            
            // Docentes
            'ver_docentes',
            'crear_docentes',
            'editar_docentes',
            'eliminar_docentes',
            
            // Programas
            'ver_programas',
            'crear_programas',
            'editar_programas',
            'eliminar_programas',
            'coordinar_programas',
            
            // Certificaciones
            'ver_certificaciones',
            'crear_certificaciones',
            'editar_certificaciones',
            'eliminar_certificaciones',
            'emitir_certificaciones',
            
            // Tesis
            'ver_tesis',
            'crear_tesis',
            'editar_tesis',
            'eliminar_tesis',
            'aprobar_tesis',
            
            // Excel
            'cargar_excel',
            'procesar_excel',
            'eliminar_cargas_excel',
            'reprocesar_excel',
            
            // Reportes
            'ver_reportes',
            'generar_reportes',
            'exportar_reportes',
            
            // Dashboard
            'ver_dashboard',
            'ver_estadisticas_dashboard',
            'acceder_acciones_rapidas',
            
            // Datos Académicos
            'ver_datos_academicos',
            'exportar_datos_academicos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $directorRole = Role::firstOrCreate(['name' => 'director', 'guard_name' => 'web']);
        $secretarioRole = Role::firstOrCreate(['name' => 'secretario', 'guard_name' => 'web']);

        // Asignar permisos a roles
        
        // Administrador: todos los permisos
        $adminRole->givePermissionTo(Permission::all());

        // Director: permisos de supervisión y aprobación
        $directorPermissions = [
            'ver_usuarios',
            'ver_gestiones',
            'activar_gestiones',
            'ver_docentes',
            'ver_programas',
            'coordinar_programas',
            'ver_certificaciones',
            'emitir_certificaciones',
            'ver_tesis',
            'aprobar_tesis',
            'ver_reportes',
            'generar_reportes',
            'exportar_reportes',
            'ver_dashboard',
            'ver_estadisticas_dashboard',
            'ver_datos_academicos',
            'exportar_datos_academicos',
        ];
        $directorRole->syncPermissions($directorPermissions);

        // Secretario: permisos de gestión operativa
        $secretarioPermissions = [
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'ver_gestiones',
            'crear_gestiones',
            'editar_gestiones',
            'ver_docentes',
            'crear_docentes',
            'editar_docentes',
            'ver_programas',
            'crear_programas',
            'editar_programas',
            'ver_certificaciones',
            'crear_certificaciones',
            'editar_certificaciones',
            'ver_tesis',
            'crear_tesis',
            'editar_tesis',
            'cargar_excel',
            'procesar_excel',
            'eliminar_cargas_excel',
            'reprocesar_excel',
            'ver_reportes',
            'generar_reportes',
            'ver_dashboard',
            'ver_estadisticas_dashboard',
            'acceder_acciones_rapidas',
            'ver_datos_academicos',
            'exportar_datos_academicos',
        ];
        $secretarioRole->syncPermissions($secretarioPermissions);

        // Crear usuario administrador por defecto
        $admin = User::firstOrCreate([
            'email' => 'admin@postgrado.uagrm.edu.bo'
        ], [
            'name' => 'Administrador Sistema',
            'password' => Hash::make('admin123')
        ]);

        $admin->assignRole('administrador');

        // Crear usuario director por defecto
        $director = User::firstOrCreate([
            'email' => 'director@postgrado.uagrm.edu.bo'
        ], [
            'name' => 'Director Postgrado',
            'password' => Hash::make('director123')
        ]);

        $director->assignRole('director');

        // Crear usuario secretario por defecto
        $secretario = User::firstOrCreate([
            'email' => 'secretario@postgrado.uagrm.edu.bo'
        ], [
            'name' => 'Secretario Académico',
            'password' => Hash::make('secretario123')
        ]);

        $secretario->assignRole('secretario');

        $this->command->info('Roles, permisos y usuarios por defecto creados exitosamente');
        $this->command->info('Usuarios creados:');
        $this->command->info('- admin@postgrado.uagrm.edu.bo / admin123 (Administrador)');
        $this->command->info('- director@postgrado.uagrm.edu.bo / director123 (Director)');
        $this->command->info('- secretario@postgrado.uagrm.edu.bo / secretario123 (Secretario)');
    }
}
