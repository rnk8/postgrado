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
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            $table->string('cod_facultad'); // Del Excel
            $table->string('nombre_facultad'); // Del Excel
            $table->string('cod_carrera'); // Del Excel
            $table->string('nombre_carrera'); // Del Excel
            $table->string('cod_plan'); // Del Excel
            $table->enum('tipo', ['pregrado', 'maestria', 'doctorado', 'posgrado'])->default('maestria');
            $table->enum('modalidad', ['presencial', 'virtual', 'semipresencial'])->default('presencial');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->foreignId('coordinador_id')->nullable()->constrained('docentes')->onDelete('set null');
            $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['cod_carrera', 'gestion_id']); // Un programa por gestión
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};
