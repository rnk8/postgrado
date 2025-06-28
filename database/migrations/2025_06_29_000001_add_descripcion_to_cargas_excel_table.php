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
        Schema::table('cargas_excel', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('ruta_archivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cargas_excel', function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });
    }
}; 