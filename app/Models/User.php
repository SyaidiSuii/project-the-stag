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
        'is_super_admin',
        'dob',
        'points_balance',
        'last_checkin_date',
        'checkin_streak',
        'assigned_station_id',
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
     * Get the kitchen station assigned to this user
     */
    public function assignedStation()
    {
        return $this->belongsTo(KitchenStation::class, 'assigned_station_id');
    }

    /**
     * Check if user is kitchen staff
     */
    public function isKitchenStaff()
    {
        return $this->hasRole('kitchen_staff');
    }

    /**
     * Check if user has access to view all stations
     */
    public function canViewAllStations()
    {
        return $this->hasAnyRole(['admin', 'manager']) || $this->is_super_admin;
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
      public function customerVouchers()
      {
          return $this->hasManyThrough(CustomerVoucher::class, CustomerProfile::class);
      }

      public function availableVouchers()
      {
          return $this->hasManyThrough(CustomerVoucher::class, CustomerProfile::class)
                      ->where('status', 'active')
                      ->where(function($query) {
                          $query->whereNull('expiry_date')
                                ->orWhere('expiry_date', '>=', now());
                      });
      }

      public function usedVouchers()
      {
          return $this->hasManyThrough(CustomerVoucher::class, CustomerProfile::class)
                      ->where('status', 'redeemed');
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

      /**
       * Get user's current loyalty tier based on total spending
       */
      public function getLoyaltyTier()
      {
          // Calculate total spending from paid orders
          $totalSpending = $this->orders()
              ->where('payment_status', 'paid')
              ->sum('total_amount');

          // Get the highest tier the user qualifies for
          return LoyaltyTier::active()
              ->where('minimum_spending', '<=', $totalSpending)
              ->orderBy('minimum_spending', 'desc')
              ->first();
      }

      /**
       * Get total spending amount
       */
      public function getTotalSpending()
      {
          return $this->orders()
              ->where('payment_status', 'paid')
              ->sum('total_amount');
      }

      // Generate voucher dari template
      public function generateVoucherFromTemplate(VoucherTemplate $template)
      {
          // Ensure user has customer profile
          if (!$this->customerProfile) {
              throw new \Exception('User must have a customer profile to generate vouchers');
          }

          return CustomerVoucher::create([
              'customer_profile_id' => $this->customerProfile->id,
              'voucher_template_id' => $template->id,
              'status' => 'active',
              'expiry_date' => $template->expiry_days ? now()->addDays($template->expiry_days) : null
          ]);
      }
}
