<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(Programa::class, 'cod_carrera', 'cod_carrera');
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'cod_doc', 'cod_doc');
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
}
