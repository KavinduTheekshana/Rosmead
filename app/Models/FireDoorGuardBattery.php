<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FireDoorGuardBattery extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintain_id',
        'replaced_date',
        'notes',
    ];

    protected $casts = [
        'replaced_date' => 'date',
    ];

    /**
     * Get the maintenance record that owns the battery replacement.
     */
    public function maintain(): BelongsTo
    {
        return $this->belongsTo(Maintain::class);
    }
}
