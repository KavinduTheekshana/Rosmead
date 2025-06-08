<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maintain extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'room_number',
        'resident_name',
        'bed_type',
        'has_bed_rails',
        'has_bed_rail_covers',
        'mattress_type',
        'air_mattress_machine_type',
        'has_sensor_mat',
        'has_ceiling_light',
        'has_ceiling_fan',
        'has_wall_light',
        'has_bathroom_light',
        'has_ac',
        'has_door_lock',
        'door_lock_pin',
        'has_tv',
        'tv_model',
        'has_tv_remote',
        'tv_place',
        // Remove the JSON fields as they'll be in separate tables
        // 'comments',
        // 'window_lock_checked',
        // 'fire_door_guard_checked',
        // 'fire_door_guard_battery_replaced',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_bed_rails' => 'boolean',
        'has_bed_rail_covers' => 'boolean',
        'has_sensor_mat' => 'boolean',
        'has_ceiling_light' => 'boolean',
        'has_ceiling_fan' => 'boolean',
        'has_wall_light' => 'boolean',
        'has_bathroom_light' => 'boolean',
        'has_ac' => 'boolean',
        'has_door_lock' => 'boolean',
        'has_tv' => 'boolean',
        'has_tv_remote' => 'boolean',
        // Remove JSON casts as they're now in separate tables
        // 'window_lock_checked' => 'array',
        // 'fire_door_guard_checked' => 'array',
        // 'fire_door_guard_battery_replaced' => 'array',
        // 'comments' => 'array',
    ];

    /**
     * Get the user that owns the maintenance record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all comments for this maintenance record.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(MaintenanceComment::class);
    }

    /**
     * Get all fire door guard checks for this maintenance record.
     */
    public function fireDoorGuardChecks(): HasMany
    {
        return $this->hasMany(FireDoorGuardCheck::class);
    }

    /**
     * Get all fire door guard battery replacements for this maintenance record.
     */
    public function fireDoorGuardBatteryReplacements(): HasMany
    {
        return $this->hasMany(FireDoorGuardBattery::class);
    }

    /**
     * Get comments for a specific year
     */
    public function commentsForYear(int $year): HasMany
    {
        return $this->comments()->whereYear('date', $year);
    }

    /**
     * Get fire door guard checks for a specific year
     */
    public function fireDoorGuardChecksForYear(int $year): HasMany
    {
        return $this->fireDoorGuardChecks()->whereYear('checked_date', $year);
    }

    /**
     * Get fire door guard battery replacements for a specific year
     */
    public function fireDoorGuardBatteryReplacementsForYear(int $year): HasMany
    {
        return $this->fireDoorGuardBatteryReplacements()->whereYear('replaced_date', $year);
    }

    // Remove the old JSON setter methods as they're no longer needed
    // public function setWindowLockCheckedAttribute($value) { ... }
    // public function setFireDoorGuardCheckedAttribute($value) { ... }
    // public function setFireDoorGuardBatteryReplacedAttribute($value) { ... }
    // public function setCommentsAttribute($value) { ... }
}