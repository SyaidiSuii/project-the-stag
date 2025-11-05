<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckinSetting extends Model
{
    use HasFactory;

    protected $fillable = ['daily_points', 'streak_milestones'];

    protected $casts = [
        'daily_points' => 'array',
        'streak_milestones' => 'array'
    ];
}