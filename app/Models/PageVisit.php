<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $route
 * @property int $visitas
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageVisit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageVisit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageVisit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageVisit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageVisit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageVisit whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageVisit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PageVisit whereVisitas($value)
 * @mixin \Eloquent
 */
class PageVisit extends Model
{
    protected $fillable = [
        'route',
        'visitas',
    ];
} 