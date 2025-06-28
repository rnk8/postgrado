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
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->string('cod_doc'); // Del Excel
            $table->string('nombre_doc'); // Del Excel
            $table->string('genero_doc'); // Del Excel
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['cod_doc', 'gestion_id']); // Un docente por gestión
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
