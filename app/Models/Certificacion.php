<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $numero
 * @property string $nro_registro_est
 * @property string $nombre_est
 * @property string $genero_est
 * @property numeric|null $nota
 * @property numeric|null $nota_defensa_tfg
 * @property \Illuminate\Support\Carbon|null $fecha_defensa_tfg
 * @property \Illuminate\Support\Carbon|null $fecha_emision
 * @property numeric|null $promedio
 * @property string $estado
 * @property int $programa_id
 * @property int $gestion_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Gestion $gestion
 * @property-read \App\Models\Programa $programa
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion emitido()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion gestionActual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereFechaDefensaTfg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereFechaEmision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereGeneroEst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereGestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereNombreEst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereNota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereNotaDefensaTfg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereNroRegistroEst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereProgramaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion wherePromedio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificacion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
