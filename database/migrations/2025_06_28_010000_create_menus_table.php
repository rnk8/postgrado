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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->cascadeOnDelete();
            $table->string('titulo');
            $table->string('ruta')->nullable();
            $table->string('icono')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->string('permiso')->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('is_external')->default(false);
            $table->timestamps();
        });

        // Ya no es necesaria la tabla pivote, los permisos controlan el acceso.
        // Schema::create('menu_role', function (Blueprint $table) {
        //     $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
        //     $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
        //     $table->primary(['menu_id', 'role_id']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('menu_role');
        Schema::dropIfExists('menus');
    }
}; 