<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardsContent extends Model
{
    use HasFactory;

    protected $table = 'rewards_contents';
    protected $fillable = ['main_title', 'points_label', 'checkin_header', 'checkin_description'];
}