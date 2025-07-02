<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $cod_doc
 * @property string $nombre_doc
 * @property string $genero_doc
 * @property string|null $email
 * @property string|null $telefono
 * @property string $estado
 * @property int $gestion_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Gestion $gestion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Programa> $programasCoordinados
 * @property-read int|null $programas_coordinados_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tesis> $tesisTutoradas
 * @property-read int|null $tesis_tutoradas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente activo()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente gestionActual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereCodDoc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereGeneroDoc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereGestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereNombreDoc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Docente whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Docente extends Model
{
    use HasFactory;

    protected $fillable = [
        'cod_doc',
        'nombre_doc',
        'genero_doc',
        'email',
        'telefono',
        'estado',
        'gestion_id'
    ];

    // Relaciones
    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function programasCoordinados()
    {
        return $this->hasMany(Programa::class, 'coordinador_id');
    }

    public function tesisTutoradas()
    {
        return $this->hasMany(Tesis::class, 'tutor_id');
    }

    public function datosAcademicos()
    {
        return $this->hasMany(DatoAcademico::class, 'cod_doc', 'cod_doc')
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

    // Métodos estáticos
    public static function crearDesdeExcel($datos, $gestionId)
    {
        return self::updateOrCreate(
            [
                'cod_doc' => $datos['cod_doc'],
                'gestion_id' => $gestionId
            ],
            [
                'nombre_doc' => $datos['nombre_doc'],
                'genero_doc' => $datos['genero_doc'],
                'estado' => 'activo'
            ]
        );
    }
}
