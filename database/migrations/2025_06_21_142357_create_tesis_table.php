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
        Schema::create('tesis', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // Autogenerado
            $table->string('titulo')->nullable(); // Se puede asignar manualmente
            $table->string('nro_registro_est'); // Del Excel
            $table->string('nombre_est'); // Del Excel
            $table->date('fecha_defensa_tfg')->nullable(); // Del Excel
            $table->decimal('nota_defensa_tfg', 5, 2)->nullable(); // Del Excel
            $table->enum('estado', ['en_proceso', 'defendida', 'aprobada', 'rechazada'])->default('en_proceso');
            $table->foreignId('tutor_id')->nullable()->constrained('docentes')->onDelete('set null');
            $table->foreignId('programa_id')->constrained('programas')->onDelete('cascade');
            $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis');
    }
};
