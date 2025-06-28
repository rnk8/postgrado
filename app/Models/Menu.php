<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'titulo',
        'ruta',
        'icono',
        'orden',
        'is_external',
    ];

    /* -------------------- Relaciones -------------------- */

    /**
     * Menú padre.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Submenús.
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('orden');
    }

    /**
     * Roles que pueden ver este menú.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'menu_role');
    }

    /* -------------------- Scopes -------------------- */

    public function scopeForRoles($query, array $rolesIds)
    {
        return $query->whereHas('roles', function ($q) use ($rolesIds) {
            $q->whereIn('roles.id', $rolesIds);
        });
    }
} 