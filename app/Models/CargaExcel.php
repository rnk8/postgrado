<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $nombre_archivo
 * @property string $ruta_archivo
 * @property string $estado
 * @property int $registros_procesados
 * @property int $registros_exitosos
 * @property int $registros_con_error
 * @property array<array-key, mixed>|null $resumen_procesamiento
 * @property \Illuminate\Support\Carbon|null $fecha_procesamiento
 * @property int $user_id
 * @property int $gestion_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $descripcion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DatoAcademico> $datosAcademicos
 * @property-read int|null $datos_academicos_count
 * @property-read \App\Models\Gestion $gestion
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel completado()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel error()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel gestionActual()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereFechaProcesamiento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereGestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereNombreArchivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereRegistrosConError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereRegistrosExitosos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereRegistrosProcesados($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereResumenProcesamiento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereRutaArchivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CargaExcel whereUserId($value)
 * @mixin \Eloquent
 */
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
