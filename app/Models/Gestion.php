<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    use HasFactory;

    protected $table = 'gestiones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'es_actual'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'es_actual' => 'boolean'
        ];
    }

    // Relaciones
    public function docentes()
    {
        return $this->hasMany(Docente::class);
    }

    public function programas()
    {
        return $this->hasMany(Programa::class);
    }

    public function certificaciones()
    {
        return $this->hasMany(Certificacion::class);
    }

    public function tesis()
    {
        return $this->hasMany(Tesis::class);
    }

    public function cargasExcel()
    {
        return $this->hasMany(CargaExcel::class);
    }

    public function datosAcademicos()
    {
        return $this->hasMany(DatoAcademico::class);
    }

    // Scopes
    public function scopeActual($query)
    {
        return $query->where('es_actual', true);
    }

    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    // Métodos estáticos
    public static function getActual()
    {
        return self::where('es_actual', true)->first();
    }

    public static function activar($id)
    {
        // Desactivar todas las gestiones
        self::query()->update(['es_actual' => false]);
        
        // Activar la gestión seleccionada
        return self::find($id)->update(['es_actual' => true]);
    }
}
