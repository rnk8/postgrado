<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificacion extends Model
{
    use HasFactory;

    protected $table = 'certificaciones';

    protected $fillable = [
        'numero',
        'nro_registro_est',
        'nombre_est',
        'genero_est',
        'nota',
        'nota_defensa_tfg',
        'fecha_defensa_tfg',
        'fecha_emision',
        'promedio',
        'estado',
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
            'fecha_emision' => 'date',
            'nota' => 'decimal:2',
            'nota_defensa_tfg' => 'decimal:2',
            'promedio' => 'decimal:2'
        ];
    }

    // Relaciones
    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    // Scopes
    public function scopeEmitido($query)
    {
        return $query->where('estado', 'emitido');
    }

    public function scopeGestionActual($query)
    {
        return $query->whereHas('gestion', function($q) {
            $q->where('es_actual', true);
        });
    }

    // Métodos
    public function calcularPromedio()
    {
        if ($this->nota && $this->nota_defensa_tfg) {
            $this->promedio = ($this->nota + $this->nota_defensa_tfg) / 2;
            $this->save();
        }
    }



    // Métodos estáticos
    public static function crearDesdeExcel($datos, $programaId, $gestionId)
    {
        if (!isset($datos['fecha_defensa_tfg']) || !$datos['fecha_defensa_tfg']) {
            return null; // No crear certificación sin fecha de defensa
        }

        // Generar número antes de crear
        $year = now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;
        $numero = "CERT-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        $certificacion = self::create([
            'numero' => $numero,
            'nro_registro_est' => $datos['nro_registro_est'],
            'nombre_est' => $datos['nombre_est'],
            'genero_est' => $datos['genero_est'],
            'nota' => $datos['nota'] ?? null,
            'nota_defensa_tfg' => $datos['nota_defensa_tfg'] ?? null,
            'fecha_defensa_tfg' => $datos['fecha_defensa_tfg'],
            'programa_id' => $programaId,
            'gestion_id' => $gestionId,
            'estado' => 'pendiente'
        ]);

        $certificacion->calcularPromedio();

        return $certificacion;
    }
}
