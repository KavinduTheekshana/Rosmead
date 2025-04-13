<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'comments',
        'window_lock_checked',
        'fire_door_guard_checked',
        'fire_door_guard_battery_replaced',
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
        'window_lock_checked' => 'array',
        'fire_door_guard_checked' => 'array',
        'fire_door_guard_battery_replaced' => 'array',
        'comments' => 'array',
    ];

    /**
     * Get the user that owns the maintenance record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set the window lock checked attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setWindowLockCheckedAttribute($value)
    {
        $this->attributes['window_lock_checked'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Set the fire door guard checked attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setFireDoorGuardCheckedAttribute($value)
    {
        $this->attributes['fire_door_guard_checked'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Set the fire door guard battery replaced attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setFireDoorGuardBatteryReplacedAttribute($value)
    {
        $this->attributes['fire_door_guard_battery_replaced'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Set the comments attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setCommentsAttribute($value)
    {
        $this->attributes['comments'] = is_array($value) ? json_encode($value) : $value;
    }
}
