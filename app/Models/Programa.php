<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $cod_facultad
 * @property string $nombre_facultad
 * @property string $cod_carrera
 * @property string $nombre_carrera
 * @property string $cod_plan
 * @property string $tipo
 * @property string $modalidad
 * @property string $estado
 * @property int|null $coordinador_id
 * @property int $gestion_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Certificacion> $certificaciones
 * @property-read int|null $certificaciones_count
 * @property-read \App\Models\Docente|null $coordinador
 * @property-read \App\Models\Gestion $gestion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tesis> $tesis
 * @property-read int|null $tesis_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa activo()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa gestionActual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa porTipo($tipo)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereCodCarrera($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereCodFacultad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereCodPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereCoordinadorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereGestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereModalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereNombreCarrera($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereNombreFacultad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programa whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Programa extends Model
{
    use HasFactory;

    protected $fillable = [
        'cod_facultad',
        'nombre_facultad',
        'cod_carrera',
        'nombre_carrera',
        'cod_plan',
        'tipo',
        'modalidad',
        'estado',
        'coordinador_id',
        'gestion_id'
    ];

    // Relaciones
    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function coordinador()
    {
        return $this->belongsTo(Docente::class, 'coordinador_id');
    }

    public function certificaciones()
    {
        return $this->hasMany(Certificacion::class);
    }

    public function tesis()
    {
        return $this->hasMany(Tesis::class);
    }

    public function datosAcademicos()
    {
        return $this->hasMany(DatoAcademico::class, 'cod_carrera', 'cod_carrera')
            ->where('datos_academicos.gestion_id', $this->gestion_id);
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeGestionActual($query)
    {
        return $query->whereHas('gestion', function($q) {
            $q->where('es_actual', true);
        });
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Métodos estáticos
    public static function crearDesdeExcel($datos, $gestionId)
    {
        return self::updateOrCreate(
            [
                'cod_carrera' => $datos['cod_carrera'],
                'gestion_id' => $gestionId
            ],
            [
                'cod_facultad' => $datos['cod_facultad'],
                'nombre_facultad' => $datos['nombre_facultad'],
                'nombre_carrera' => $datos['nombre_carrera'],
                'cod_plan' => $datos['cod_plan'],
                'tipo' => 'maestria', // Por defecto
                'modalidad' => 'presencial', // Por defecto
                'estado' => 'activo'
            ]
        );
    }
}
