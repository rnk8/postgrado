<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
