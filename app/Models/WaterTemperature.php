<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class WaterTemperature extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'year',
        'month',
        'cold_temp',
        'hot_temp',
        'has_fault',
        'action_taken',
        'check_date',
    ];

    protected $casts = [
        'cold_temp' => 'decimal:2',
        'hot_temp' => 'decimal:2',
        'has_fault' => 'boolean',
        'check_date' => 'date',
        'year' => 'integer',
        'month' => 'integer',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($waterTemp) {
            $waterTemp->user_id = Auth::id();
            
            // Auto-fill year and month if not provided
            if (!isset($waterTemp->year)) {
                $waterTemp->year = now()->year;
            }
            
            if (!isset($waterTemp->month)) {
                $waterTemp->month = now()->month;
            }
        });
    }

    // Relations remain the same
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_number', 'room_number');
    }
    
    // Helper method to get month name
    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::create()->month($this->month)->format('F');
    }
}