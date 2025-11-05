<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFeedback extends Model
{
    use HasFactory;

    protected $table = 'customer_feedbacks';

    protected $fillable = [
        'user_id',
        'rating',
        'name',
        'email',
        'message',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Get the user that submitted this feedback.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
