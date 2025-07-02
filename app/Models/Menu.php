<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

/**
 * 
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $titulo
 * @property string|null $ruta
 * @property string|null $icono
 * @property int $orden
 * @property string|null $permiso
 * @property bool $activo
 * @property bool $is_external
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Menu> $children
 * @property-read int|null $children_count
 * @property-read Menu|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu forRoles(array $rolesIds)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereIcono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereIsExternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu wherePermiso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereRuta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereTitulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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