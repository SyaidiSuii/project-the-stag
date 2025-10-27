<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StationType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'station_type',
        'icon',
    ];

    /**
     * Get kitchen stations for this type
     */
    public function kitchenStations()
    {
        return $this->hasMany(KitchenStation::class, 'station_type_id');
    }
}
