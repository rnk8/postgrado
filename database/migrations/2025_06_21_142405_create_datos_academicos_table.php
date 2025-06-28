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
        Schema::create('datos_academicos', function (Blueprint $table) {
            $table->id();
            // Datos de facultad y carrera
            $table->string('cod_facultad')->nullable();
            $table->string('nombre_facultad')->nullable();
            $table->string('cod_carrera')->nullable();
            $table->string('cod_plan')->nullable();
            $table->string('nombre_carrera')->nullable();
            
            // Datos de materia
            $table->string('cod_materia_plan')->nullable();
            $table->string('cod_grupo')->nullable();
            $table->string('cod_edicion')->nullable();
            $table->string('cod_modalidad')->nullable();
            $table->string('sigla_materia')->nullable();
            $table->string('nombre_materia')->nullable();
            $table->date('fecha_ini')->nullable();
            $table->date('fecha_fin')->nullable();
            
            // Datos del docente
            $table->string('cod_doc')->nullable();
            $table->string('nombre_doc')->nullable();
            $table->string('genero_doc')->nullable();
            
            // Datos del estudiante
            $table->string('nro_registro_est')->nullable();
            $table->string('nombre_est')->nullable();
            $table->string('genero_est')->nullable();
            $table->decimal('nota', 5, 2)->nullable();
            $table->string('acta_cerrada')->nullable(); // S/N
            $table->string('matriculado')->nullable(); // S/N
            
            // Datos de tesis
            $table->date('fecha_defensa_tfg')->nullable();
            $table->decimal('nota_defensa_tfg', 5, 2)->nullable();
            
            // Relaciones
            $table->foreignId('carga_excel_id')->constrained('cargas_excel')->onDelete('cascade');
            $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_academicos');
    }
};
