<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
