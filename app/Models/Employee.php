<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'phone_number',
        'address',
        'position',
        'salary',
        'hire_date',
        'experience_details',
        'date_of_birth',
        'user_id',
    ];

    /**
     * Get the user that owns the employee profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
