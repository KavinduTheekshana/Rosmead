<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FireDoorGuardCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintain_id',
        'checked_date',
        'notes',
    ];

    protected $casts = [
        'checked_date' => 'date',
    ];

    /**
     * Get the maintenance record that owns the guard check.
     */
    public function maintain(): BelongsTo
    {
        return $this->belongsTo(Maintain::class);
    }
}