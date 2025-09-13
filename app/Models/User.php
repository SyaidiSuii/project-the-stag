<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomPasswordResetNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'is_active',
        'dob',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomPasswordResetNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\CustomEmailVerificationNotification());
    }


    // Relationships
    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

     /**
       * Get formatted phone number for display
       */
      public function getFormattedPhoneAttribute()
      {
          if (!$this->phone_number) return null;

          $phoneUtil = PhoneNumberUtil::getInstance();
          try {
              $numberProto = $phoneUtil->parse($this->phone_number, null);
              return $phoneUtil->format($numberProto, PhoneNumberFormat::INTERNATIONAL);
          } catch (\Exception $e) {
              return $this->phone_number; // Fallback to original
          }
      }

      /**
       * Get local phone number format
       */
      public function getLocalPhoneAttribute()
      {
          if (!$this->phone_number) return null;

          $phoneUtil = PhoneNumberUtil::getInstance();
          try {
              $numberProto = $phoneUtil->parse($this->phone_number, null);
              return $phoneUtil->format($numberProto, PhoneNumberFormat::NATIONAL);
          } catch (\Exception $e) {
              return $this->phone_number;
          }
      }
}
