<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon $fecha_fin
 * @property string $estado
 * @property bool $es_actual
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CargaExcel> $cargasExcel
 * @property-read int|null $cargas_excel_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Certificacion> $certificaciones
 * @property-read int|null $certificaciones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DatoAcademico> $datosAcademicos
 * @property-read int|null $datos_academicos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Docente> $docentes
 * @property-read int|null $docentes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Programa> $programas
 * @property-read int|null $programas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tesis> $tesis
 * @property-read int|null $tesis_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion activo()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion actual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereEsActual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
}
