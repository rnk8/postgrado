<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tesis extends Model
{
    use HasFactory;

    protected $table = 'tesis';

    protected $fillable = [
        'codigo',
        'titulo',
        'nro_registro_est',
        'nombre_est',
        'fecha_defensa_tfg',
        'nota_defensa_tfg',
        'estado',
        'tutor_id',
        'programa_id',
        'gestion_id'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha_defensa_tfg' => 'date',
            'nota_defensa_tfg' => 'decimal:2'
        ];
    }

    // Relaciones
    public function tutor()
    {
        return $this->belongsTo(Docente::class, 'tutor_id');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    // Scopes
    public function scopeDefendida($query)
    {
        return $query->where('estado', 'defendida');
    }

    public function scopeAprobada($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopeGestionActual($query)
    {
        return $query->whereHas('gestion', function($q) {
            $q->where('es_actual', true);
        });
    }

    // Métodos


    // Métodos estáticos
    public static function crearDesdeExcel($datos, $programaId, $gestionId, $tutorId = null)
    {
        if (!isset($datos['fecha_defensa_tfg']) || !$datos['fecha_defensa_tfg']) {
            return null; // No crear tesis sin fecha de defensa
        }

        // Generar código antes de crear
        $year = now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;
        $codigo = "TESIS-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Generar título
        $titulo = "Trabajo Final de Grado - {$datos['nombre_est']}";

        $tesis = self::create([
            'codigo' => $codigo,
            'titulo' => $titulo,
            'nro_registro_est' => $datos['nro_registro_est'],
            'nombre_est' => $datos['nombre_est'],
            'fecha_defensa_tfg' => $datos['fecha_defensa_tfg'],
            'nota_defensa_tfg' => $datos['nota_defensa_tfg'] ?? null,
            'tutor_id' => $tutorId,
            'programa_id' => $programaId,
            'gestion_id' => $gestionId,
            'estado' => 'defendida'
        ]);

        return $tesis;
    }
}
