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
        Schema::create('certificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // Autogenerado
            $table->string('nro_registro_est'); // Del Excel
            $table->string('nombre_est'); // Del Excel
            $table->string('genero_est'); // Del Excel
            $table->decimal('nota', 5, 2)->nullable(); // Del Excel
            $table->decimal('nota_defensa_tfg', 5, 2)->nullable(); // Del Excel
            $table->date('fecha_defensa_tfg')->nullable(); // Del Excel
            $table->date('fecha_emision')->nullable();
            $table->decimal('promedio', 5, 2)->nullable();
            $table->enum('estado', ['emitido', 'pendiente', 'anulado'])->default('pendiente');
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
        Schema::dropIfExists('certificaciones');
    }
};
