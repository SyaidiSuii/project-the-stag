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
        'points_balance',
        'last_checkin_date',
        'checkin_streak',
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
        'last_checkin_date' => 'date',
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

    // Voucher & Rewards Relationships
    public function userVouchers()
    {
        return $this->hasMany(UserVoucher::class);
    }

    public function availableVouchers()
    {
        return $this->hasMany(UserVoucher::class)->available();
    }

    public function usedVouchers()
    {
        return $this->hasMany(UserVoucher::class)->used();
    }

    public function userPromotions()
    {
        return $this->hasMany(UserPromotion::class);
    }

    public function exchangePointRedemptions()
    {
        return $this->hasMany(ExchangePointRedemption::class);
    }

    // Points & Loyalty methods
    public function addPoints($points, $reason = null)
    {
        $currentPoints = $this->points_balance ?? 0;
        $this->update(['points_balance' => $currentPoints + $points]);

        // Log points transaction if needed
        // PointsTransaction::create([...]);

        return $this;
    }

    public function deductPoints($points, $reason = null)
    {
        $currentPoints = $this->points_balance ?? 0;
        if ($currentPoints >= $points) {
            $this->update(['points_balance' => $currentPoints - $points]);
            return true;
        }
        return false;
    }

    public function hasEnoughPoints($points)
    {
        return ($this->points_balance ?? 0) >= $points;
    }

    // Generate voucher dari template
    public function generateVoucherFromTemplate(VoucherCollection $template)
    {
        return UserVoucher::create([
            'user_id' => $this->id,
            'voucher_collection_id' => $template->id,
            'discount_type' => $template->voucher_type,
            'discount_value' => $template->voucher_value,
            'minimum_order' => $template->spending_requirement,
            'expires_at' => $template->valid_until,
            'status' => 'available'
        ]);
    }
}
