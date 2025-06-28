<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargaExcel extends Model
{
    use HasFactory;

    protected $table = 'cargas_excel';

    protected $fillable = [
        'nombre_archivo',
        'ruta_archivo',
        'descripcion',
        'estado',
        'registros_procesados',
        'registros_exitosos',
        'registros_con_error',
        'resumen_procesamiento',
        'fecha_procesamiento',
        'user_id',
        'gestion_id'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'resumen_procesamiento' => 'array',
            'fecha_procesamiento' => 'datetime'
        ];
    }

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function datosAcademicos()
    {
        return $this->hasMany(DatoAcademico::class);
    }

    // Scopes
    public function scopeCompletado($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeError($query)
    {
        return $query->where('estado', 'error');
    }

    public function scopeGestionActual($query)
    {
        return $query->whereHas('gestion', function($q) {
            $q->where('es_actual', true);
        });
    }

    // Métodos
    public function marcarComoProcesando()
    {
        $this->update([
            'estado' => 'procesando',
            'fecha_procesamiento' => now()
        ]);
    }

    public function marcarComoCompletado($resumen = [])
    {
        $this->update([
            'estado' => 'completado',
            'resumen_procesamiento' => $resumen
        ]);
    }

    public function marcarComoError($error = null)
    {
        $resumen = $this->resumen_procesamiento ?? [];
        if ($error) {
            $resumen['error'] = $error;
        }
        
        $this->update([
            'estado' => 'error',
            'resumen_procesamiento' => $resumen
        ]);
    }

    public function actualizarContadores($procesados, $exitosos, $errores)
    {
        $this->update([
            'registros_procesados' => $procesados,
            'registros_exitosos' => $exitosos,
            'registros_con_error' => $errores
        ]);
    }
}
