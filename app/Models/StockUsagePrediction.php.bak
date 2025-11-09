<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockUsagePrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'prediction_date',
        'predicted_usage',
        'actual_usage',
        'accuracy_score',
        'metadata',
    ];

    protected $casts = [
        'prediction_date' => 'date',
        'predicted_usage' => 'decimal:2',
        'actual_usage' => 'decimal:2',
        'accuracy_score' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the stock item
     */
    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    /**
     * Calculate accuracy based on actual vs predicted
     */
    public function calculateAccuracy()
    {
        if ($this->actual_usage === null || $this->predicted_usage == 0) {
            return null;
        }

        $difference = abs($this->actual_usage - $this->predicted_usage);
        $accuracy = (1 - ($difference / max($this->actual_usage, $this->predicted_usage))) * 100;

        $this->accuracy_score = max(0, min(100, $accuracy));
        $this->save();

        return $this->accuracy_score;
    }

    /**
     * Scope: Future predictions
     */
    public function scopeFuture($query)
    {
        return $query->where('prediction_date', '>=', now()->toDateString());
    }

    /**
     * Scope: Past predictions
     */
    public function scopePast($query)
    {
        return $query->where('prediction_date', '<', now()->toDateString());
    }
}
