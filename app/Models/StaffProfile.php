<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'roles_id',
        'user_id',
        'phone_number',
        'address',
        'position',
        'experience',
        'photo',
        'salary',
        'hire_date',
        'emergency_contact',
        'emergency_phone',
    ];


    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
    ];

    
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'roles_id');
    }
}
