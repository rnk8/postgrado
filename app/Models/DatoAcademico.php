<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string|null $cod_facultad
 * @property string|null $nombre_facultad
 * @property string|null $cod_carrera
 * @property string|null $cod_plan
 * @property string|null $nombre_carrera
 * @property string|null $cod_materia_plan
 * @property string|null $cod_grupo
 * @property string|null $cod_edicion
 * @property string|null $cod_modalidad
 * @property string|null $sigla_materia
 * @property string|null $nombre_materia
 * @property \Illuminate\Support\Carbon|null $fecha_ini
 * @property \Illuminate\Support\Carbon|null $fecha_fin
 * @property string|null $cod_doc
 * @property string|null $nombre_doc
 * @property string|null $genero_doc
 * @property string|null $nro_registro_est
 * @property string|null $nombre_est
 * @property string|null $genero_est
 * @property numeric|null $nota
 * @property string|null $acta_cerrada
 * @property string|null $matriculado
 * @property \Illuminate\Support\Carbon|null $fecha_defensa_tfg
 * @property numeric|null $nota_defensa_tfg
 * @property int $carga_excel_id
 * @property int $gestion_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CargaExcel $cargaExcel
 * @property-read \App\Models\Docente|null $docente
 * @property-read \App\Models\Gestion $gestion
 * @property-read \App\Models\Programa|null $programa
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico actaCerrada()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico conDefensaTesis()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico gestionActual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico matriculados()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico porCarrera($codCarrera)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico porDocente($codDoc)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereActaCerrada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereCargaExcelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereCodCarrera($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereCodDoc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereCodEdicion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereCodFacultad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereCodGrupo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereCodMateriaPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereCodModalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereCodPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereFechaDefensaTfg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereFechaIni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereGeneroDoc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereGeneroEst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereGestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereMatriculado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereNombreCarrera($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereNombreDoc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereNombreEst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereNombreFacultad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereNombreMateria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereNota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereNotaDefensaTfg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereNroRegistroEst($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereSiglaMateria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatoAcademico whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DatoAcademico extends Model
{
    use HasFactory;

    protected $table = 'datos_academicos';

    protected $fillable = [
        'cod_facultad',
        'nombre_facultad', 
        'cod_carrera',
        'cod_plan',
        'nombre_carrera',
        'cod_materia_plan',
        'cod_grupo',
        'cod_edicion',
        'cod_modalidad',
        'sigla_materia',
        'nombre_materia',
        'fecha_ini',
        'fecha_fin',
        'cod_doc',
        'nombre_doc',
        'genero_doc',
        'nro_registro_est',
        'nombre_est',
        'genero_est',
        'nota',
        'acta_cerrada',
        'matriculado',
        'fecha_defensa_tfg',
        'nota_defensa_tfg',
        'carga_excel_id',
        'gestion_id'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha_ini' => 'date',
            'fecha_fin' => 'date',
            'fecha_defensa_tfg' => 'date',
            'nota' => 'decimal:2',
            'nota_defensa_tfg' => 'decimal:2'
        ];
    }

    // Relaciones
    public function cargaExcel()
    {
        return $this->belongsTo(CargaExcel::class);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'cod_carrera', 'cod_carrera')
            ->where('programas.gestion_id', $this->gestion_id ?? $this->getRawOriginal('gestion_id'));
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'cod_doc', 'cod_doc')
            ->where('docentes.gestion_id', $this->gestion_id ?? $this->getRawOriginal('gestion_id'));
    }

    // Scopes
    public function scopeConDefensaTesis($query)
    {
        return $query->whereNotNull('fecha_defensa_tfg');
    }

    public function scopeMatriculados($query)
    {
        return $query->where('matriculado', 'S');
    }

    public function scopeActaCerrada($query)
    {
        return $query->where('acta_cerrada', 'S');
    }

    public function scopeGestionActual($query)
    {
        return $query->whereHas('gestion', function($q) {
            $q->where('es_actual', true);
        });
    }

    public function scopePorDocente($query, $codDoc)
    {
        return $query->where('cod_doc', $codDoc);
    }

    public function scopePorCarrera($query, $codCarrera)
    {
        return $query->where('cod_carrera', $codCarrera);
    }

    // Métodos
    public function tieneDefensaTesis()
    {
        return !is_null($this->fecha_defensa_tfg);
    }

    public function estaMatriculado()
    {
        return $this->matriculado === 'S';
    }

    public function tieneActaCerrada()
    {
        return $this->acta_cerrada === 'S';
    }

    // Nuevos métodos para obtener objetos relacionados
    public function obtenerPrograma()
    {
        return Programa::where('cod_carrera', $this->cod_carrera)
            ->where('gestion_id', $this->gestion_id)
            ->first();
    }

    public function obtenerDocente()
    {
        return Docente::where('cod_doc', $this->cod_doc)
            ->where('gestion_id', $this->gestion_id)
            ->first();
    }

    public function obtenerCertificacion()
    {
        return Certificacion::where('nro_registro_est', $this->nro_registro_est)
            ->whereHas('programa', function($q) {
                $q->where('cod_carrera', $this->cod_carrera);
            })
            ->where('gestion_id', $this->gestion_id)
            ->first();
    }

    public function obtenerTesis()
    {
        return Tesis::where('nro_registro_est', $this->nro_registro_est)
            ->whereHas('programa', function($q) {
                $q->where('cod_carrera', $this->cod_carrera);
            })
            ->where('gestion_id', $this->gestion_id)
            ->first();
    }
}
