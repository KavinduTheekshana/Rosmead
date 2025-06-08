<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintain_id',
        'date',
        'comment',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the maintenance record that owns the comment.
     */
    public function maintain(): BelongsTo
    {
        return $this->belongsTo(Maintain::class);
    }
}