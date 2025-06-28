<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Índices para la tabla gestiones
        Schema::table('gestiones', function (Blueprint $table) {
            $table->index('es_actual');
            $table->index('estado');
        });

        // Índices para la tabla docentes
        Schema::table('docentes', function (Blueprint $table) {
            $table->index('cod_doc');
            $table->index(['gestion_id', 'estado']);
        });

        // Índices para la tabla programas
        Schema::table('programas', function (Blueprint $table) {
            $table->index('cod_carrera');
            $table->index('cod_facultad');
            $table->index(['gestion_id', 'estado']);
        });

        // Índices para la tabla certificaciones
        Schema::table('certificaciones', function (Blueprint $table) {
            $table->index('numero');
            $table->index('nro_registro_est');
            $table->index('estado');
            $table->index('fecha_defensa_tfg');
            $table->index(['gestion_id', 'estado']);
        });

        // Índices para la tabla tesis
        Schema::table('tesis', function (Blueprint $table) {
            $table->index('codigo');
            $table->index('nro_registro_est');
            $table->index('estado');
            $table->index('fecha_defensa_tfg');
            $table->index(['gestion_id', 'estado']);
        });

        // Índices para la tabla datos_academicos
        Schema::table('datos_academicos', function (Blueprint $table) {
            $table->index('cod_doc');
            $table->index('cod_carrera');
            $table->index('nro_registro_est');
            $table->index('fecha_defensa_tfg');
            $table->index(['gestion_id', 'cod_carrera']);
            $table->index(['gestion_id', 'cod_doc']);
        });

        // Índices para la tabla cargas_excel
        Schema::table('cargas_excel', function (Blueprint $table) {
            $table->index('estado');
            $table->index(['gestion_id', 'estado']);
            $table->index('fecha_procesamiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gestiones', function (Blueprint $table) {
            $table->dropIndex(['es_actual']);
            $table->dropIndex(['estado']);
        });

        Schema::table('docentes', function (Blueprint $table) {
            $table->dropIndex(['cod_doc']);
            $table->dropIndex(['gestion_id', 'estado']);
        });

        Schema::table('programas', function (Blueprint $table) {
            $table->dropIndex(['cod_carrera']);
            $table->dropIndex(['cod_facultad']);
            $table->dropIndex(['gestion_id', 'estado']);
        });

        Schema::table('certificaciones', function (Blueprint $table) {
            $table->dropIndex(['numero']);
            $table->dropIndex(['nro_registro_est']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['fecha_defensa_tfg']);
            $table->dropIndex(['gestion_id', 'estado']);
        });

        Schema::table('tesis', function (Blueprint $table) {
            $table->dropIndex(['codigo']);
            $table->dropIndex(['nro_registro_est']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['fecha_defensa_tfg']);
            $table->dropIndex(['gestion_id', 'estado']);
        });

        Schema::table('datos_academicos', function (Blueprint $table) {
            $table->dropIndex(['cod_doc']);
            $table->dropIndex(['cod_carrera']);
            $table->dropIndex(['nro_registro_est']);
            $table->dropIndex(['fecha_defensa_tfg']);
            $table->dropIndex(['gestion_id', 'cod_carrera']);
            $table->dropIndex(['gestion_id', 'cod_doc']);
        });

        Schema::table('cargas_excel', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['gestion_id', 'estado']);
            $table->dropIndex(['fecha_procesamiento']);
        });
    }
};
