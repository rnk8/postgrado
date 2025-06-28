# SISTEMA DE REPORTES ACADÉMICOS - ESCUELA DE POSTGRADO UAGRM

## Descripción

Sistema backend completo en **Laravel 12** para la gestión de reportes académicos de la Escuela de Postgrado de la UAGRM. El sistema permite **cargar archivos Excel**, **procesar datos académicos**, y **generar reportes** de manera automatizada.

## Características Principales

 
- ✅ **Sistema de roles y permisos con Spatie Permission**
- ✅ **Procesamiento inteligente de archivos Excel**
- ✅ **Gestión de gestiones académicas** (períodos)
- ✅ **CRUD completo** para docentes, programas, certificaciones y tesis
- ✅ **Reportes y estadísticas** en tiempo real
- ✅ **API RESTful** completamente funcional

## Tecnologías

- **Laravel 12** + **PHP 8.2** + **inertiav2**
- **Spatie Laravel Permission** (roles y permisos)
- **Maatwebsite Excel** (procesamiento de archivos)
- **SQLite/MySQL/PostgreSQL** (base de datos)

## Instalación y Configuración

### 1. Clonar e instalar dependencias
```bash
composer install
```

### 2. Configurar base de datos
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Ejecutar migraciones y seeders
```bash
php artisan migrate
php artisan db:seed
```

### 4. Iniciar servidor
```bash
php artisan serve
```

## Usuarios por Defecto

El sistema crea automáticamente 3 usuarios:

| Email | Password | Rol |
|-------|----------|-----|
| `admin@postgrado.uagrm.edu.bo` | `admin123` | Administrador |
| `director@postgrado.uagrm.edu.bo` | `director123` | Director |
| `secretario@postgrado.uagrm.edu.bo` | `secretario123` | Secretario |

## Estructura de la API

### Autenticación
```
POST /api/login          # Iniciar sesión
POST /api/logout         # Cerrar sesión  
GET  /api/me             # Datos del usuario actual
```

### Gestiones Académicas
```
GET    /api/gestiones           # Listar gestiones
POST   /api/gestiones           # Crear gestión
GET    /api/gestiones/{id}      # Ver gestión
PUT    /api/gestiones/{id}      # Actualizar gestión
DELETE /api/gestiones/{id}      # Eliminar gestión
POST   /api/gestiones/{id}/activar  # Activar gestión
GET    /api/gestion-actual      # Obtener gestión actual
```

### Docentes
```
GET    /api/docentes           # Listar docentes
POST   /api/docentes           # Crear docente
GET    /api/docentes/{id}      # Ver docente
PUT    /api/docentes/{id}      # Actualizar docente
DELETE /api/docentes/{id}      # Eliminar docente
GET    /api/coordinadores      # Listar coordinadores
```

### Programas Académicos
```
GET    /api/programas          # Listar programas
POST   /api/programas          # Crear programa
GET    /api/programas/{id}     # Ver programa
PUT    /api/programas/{id}     # Actualizar programa
DELETE /api/programas/{id}     # Eliminar programa
```

### Certificaciones
```
GET    /api/certificaciones    # Listar certificaciones
POST   /api/certificaciones    # Crear certificación
GET    /api/certificaciones/{id}  # Ver certificación
PUT    /api/certificaciones/{id}  # Actualizar certificación
DELETE /api/certificaciones/{id}  # Eliminar certificación
```

### Tesis
```
GET    /api/tesis              # Listar tesis
POST   /api/tesis              # Crear tesis
GET    /api/tesis/{id}         # Ver tesis
PUT    /api/tesis/{id}         # Actualizar tesis
DELETE /api/tesis/{id}         # Eliminar tesis
```

### Carga de Excel
```
GET    /api/excel             # Listar cargas
POST   /api/excel             # Subir archivo Excel
GET    /api/excel/{id}        # Ver carga
POST   /api/excel/{id}/procesar  # Procesar archivo
DELETE /api/excel/{id}        # Eliminar carga
```

### Reportes
```
GET /api/dashboard                           # Dashboard principal
GET /api/reportes/docentes-por-facultad     # Docentes por facultad
GET /api/reportes/estudiantes-por-programa  # Estudiantes por programa
GET /api/reportes/graduados-por-periodo     # Graduados por período
GET /api/reportes/resumen-certificaciones  # Resumen certificaciones
GET /api/reportes/completo                  # Reporte completo
```

## Formato Excel Esperado

El sistema puede procesar archivos Excel con las siguientes columnas (en cualquier orden):

```
cod_facultad, Nombre_facultad, cod_carrera, cod_plan, nombre_carrera,
cod_materia_plan, cod_grupo, cod_edicion, cod_modalidad, sigla_materia,
nombre_materia, fecha_ini, fecha_fin, cod_doc, nombre_doc, genero_doc,
nro_registro_est, nombre_est, genero_est, nota, acta_cerrada,
matriculado, fecha_defensa_tfg, nota_defensa_tfg
```

### Flexibilidad del Sistema

- ✅ **Detecta automáticamente** las columnas del Excel
- ✅ **Mapea inteligentemente** variaciones en nombres de columnas
- ✅ **Ignora columnas adicionales** que no reconoce
- ✅ **Maneja datos faltantes** de manera robusta

## Flujo de Trabajo

### 1. Gestión de Gestiones
- Crear gestiones académicas (2024-I, 2024-II, etc.)
- **Solo una gestión puede estar activa** a la vez
- Todos los datos se asocian a la gestión activa

### 2. Carga de Datos
- Subir archivo Excel vía API
- Sistema **mapea automáticamente** las columnas
- **Procesa en background** y crea entidades:
  - Docentes (basado en `cod_doc`)
  - Programas (basado en `cod_carrera`)
  - Certificaciones (si hay `fecha_defensa_tfg`)
  - Tesis (si hay `fecha_defensa_tfg`)

### 3. Gestión Manual
- CRUD completo para todas las entidades
- Asignación de coordinadores a programas
- Asignación de tutores a tesis
- Edición de estados y datos

### 4. Reportes
- Dashboard con estadísticas generales
- Reportes específicos por facultad, programa, etc.
- Datos filtrados por gestión activa

## Roles y Permisos

### Administrador
- Acceso total al sistema
- Gestión de usuarios
- Todas las funcionalidades

### Director
- Gestión académica completa
- No puede gestionar usuarios
- Puede activar gestiones

### Secretario
- Operaciones básicas
- Carga de Excel
- Creación/edición de entidades
- No puede eliminar ni activar gestiones

## Casos de Uso Implementados

- ✅ **CU1**: Gestión de Usuarios
- ✅ **CU2**: Gestión de Carga de Datos Excel
- ✅ **CU3**: Gestión de Gestiones
- ✅ **CU4**: Gestión de Certificaciones
- ✅ **CU5**: Gestión de Programas
- ✅ **CU6**: Gestión de Docentes
- ✅ **CU7**: Gestión de Tesis
- ✅ **CU8**: Reportes y Estadísticas

## Estructura de Base de Datos

### Tablas Principales
- `users` - Usuarios del sistema
- `gestiones` - Períodos académicos
- `docentes` - Información de docentes
- `programas` - Programas académicos
- `certificaciones` - Certificaciones emitidas
- `tesis` - Trabajos finales y tesis
- `cargas_excel` - Registro de archivos procesados
- `datos_academicos` - Datos raw del Excel

### Relaciones
- Una **gestión** puede tener muchos docentes, programas, etc.
- Un **programa** puede tener muchas certificaciones y tesis
- Un **docente** puede coordinar programas y tutorar tesis
- Cada **carga Excel** se asocia a una gestión

## Ejemplos de Uso con cURL

### 1. Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@postgrado.uagrm.edu.bo","password":"admin123"}'
```

### 2. Obtener gestión actual
```bash
curl -X GET http://localhost:8000/api/gestion-actual \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Subir archivo Excel
```bash
curl -X POST http://localhost:8000/api/excel \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "archivo=@datos_academicos.xlsx"
```

### 4. Dashboard
```bash
curl -X GET http://localhost:8000/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Ventajas del Sistema

- 🚀 **Escalable**: Arquitectura modular y bien estructurada
- 🔒 **Seguro**: Autenticación robusta y control de permisos
- 🔄 **Flexible**: Procesa cualquier formato Excel
- 📊 **Completo**: CRUD + Reportes + Estadísticas
- ⚡ **Rápido**: Optimizado para grandes volúmenes de datos
- 🛠️ **Mantenible**: Código limpio y bien documentado

## Soporte

Este sistema está diseñado específicamente para las necesidades de la **Escuela de Postgrado UAGRM** y proporciona una solución completa y robusta para la gestión de reportes académicos.

**Desarrollado con Laravel 12 + inertiav2**
