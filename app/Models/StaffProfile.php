<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\IdGeneratorService;

class StaffProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role_id',
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
        'staff_id',
        'ic_number',
    ];


    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['display_id'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($staffProfile) {
            // Auto-generate staff_id if not provided
            if (empty($staffProfile->staff_id)) {
                $idGenerator = app(IdGeneratorService::class);
                $staffProfile->staff_id = $idGenerator->generateStaffId(
                    $staffProfile->position,
                    $staffProfile->ic_number
                );
            }
        });
    }


    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get display ID attribute
     *
     * @return string|null
     */
    public function getDisplayIdAttribute()
    {
        return $this->staff_id;
    }

    /**
     * Get position code for ID generation
     *
     * @return string
     */
    public function getPositionCode()
    {
        $idGenerator = app(IdGeneratorService::class);
        return $idGenerator->getPositionCode($this->position);
    }
}
