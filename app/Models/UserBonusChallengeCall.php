<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBonusChallengeCall extends Model
{
    use HasFactory;

    protected $table = 'user_bonus_challenge_claims';

    protected $fillable = [
        'user_id',
        'bonus_point_challenge_id',
        'points_awarded',
    ];

    protected $casts = [
        'points_awarded' => 'integer',
    ];

    /**
     * Get the user who claimed this bonus challenge
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bonus challenge that was claimed
     */
    public function bonusPointChallenge(): BelongsTo
    {
        return $this->belongsTo(BonusPointChallenge::class);
    }
}
