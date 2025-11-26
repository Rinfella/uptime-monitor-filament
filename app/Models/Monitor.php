<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Monitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'check_interval_minutes',
        'status',
        'notify_on_failure',
        'notify_on_recovery',
        'consecutive_failures',
        'max_consecutive_failures',
        'is_active',
        'check_ssl_certificate',
        'ssl_certificate_expires_at',
    ];

    protected $casts = [
        'notify_on_failure' => 'boolean',
        'notify_on_recovery' => 'boolean',
        'is_active' => 'boolean',
        'check_interval_minutes' => 'integer',
        'consecutive_failures' => 'integer',
        'max_consecutive_failures' => 'integer',
        'check_ssl_certificate' => 'boolean',
        'ssl_certificate_expires_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'unknown',
        'is_active' => true,
        'notify_on_failure' => true,
        'notify_on_recovery' => true,
        'check_interval_minutes' => 2,
        'max_consecutive_failures' => 3,
        'consecutive_failures' => 0,
        'check_ssl_certificate' => true,
    ];


    public function heartbeats(): HasMany
    {
        return $this->hasMany(Heartbeat::class);
    }

    public function latestHeartbeat(): HasOne
    {
        return $this->hasOne(Heartbeat::class)->latestOfMany('checked_at');
    }

    public function latestSuccessfulHeartbeat(): HasOne
    {
        return $this->hasOne(Heartbeat::class)->where('status', 'up')->latestOfMany('checked_at');
    }

    // Get only active monitors
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Get monitors that need checking based on intervel

    public function scopeNeedsCheck(Builder $query): Builder
    {
        return $query->active()
            ->where(function ($query) {
                // Never checked before - should be checked
                $query->whereDoesntHave('heartbeats')
                    // Or last check was longer ago than the interval
                    ->orWhereHas('latestHeartbeat', function ($subQuery) {
                        $subQuery->whereRaw(
                            'checked_at <= DATE_SUB(NOW(), INTERVAL monitors.check_interval_minutes MINUTE)'
                        );
                    });
            });
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function shouldBeChecked(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // If never checked, should be checked
        $latestHeartbeat = $this->latestHeartbeat;
        if (!$latestHeartbeat) {
            return true;
        }

        // Check if enough time has passed since last check
        return $latestHeartbeat->checked_at
            ->addMinutes($this->check_interval_minutes)
            ->isPast();
    }

    // Get the last time this monitor was checked
    public function getLastCheckedAtAttribute()
    {
        return $this->latestHeartbeat?->checked_at;
    }

    // Get the last time this monitor was up
    public function getLastUpAtAttribute()
    {
        return $this->lastSuccessfulHeartbeat?->checked_at;
    }

    // Get the last time this monitor was down
    public function getLastDownAtAttribute()
    {
        return $this->lastFailedHeartbeat?->checked_at;
    }

    // Get current response time from latest heartbeat
    public function getResponseTimeAttribute()
    {
        return $this->latestHeartbeat?->response_time;
    }

    // Get current HTTP status code from latest heartbeat
    public function getHttpStatusCodeAttribute()
    {
        return $this->latestHeartbeat?->http_status_code;
    }

    // Get current error message from latest heartbeat
    public function getErrorMessageAttribute()
    {
        return $this->latestHeartbeat?->error_message;
    }

    // Calculate uptime percentage for a given period
    public function getUptimePercentage(int $days = 30): float
    {
        $startDate = now()->subDays($days);

        $totalChecks = $this->heartbeats()
            ->where('checked_at', '>=', $startDate)
            ->count();

        if ($totalChecks === 0) {
            return 0;
        }

        $upChecks = $this->heartbeats()
            ->where('checked_at', '>=', $startDate)
            ->where('status', 'up')
            ->count();

        return round(($upChecks / $totalChecks) * 100, 2);
    }

    // Get average response time for a given period
    public function getAverageResponseTime(int $days = 30): ?float
    {
        return $this->heartbeats()
            ->where('checked_at', '>=', now()->subDays($days))
            ->where('status', 'up')
            ->avg('response_time');
    }

    // Check if monitor is currently down
    public function isDown(): bool
    {
        return $this->status === 'down';
    }

    // Check if monitor is currently up
    public function isUp(): bool
    {
        return $this->status === 'up';
    }

    // Check if monitor status is unknown
    public function isUnknown(): bool
    {
        return $this->status === 'unknown';
    }

    public function getErrorMessage(): ?string
    {
        return $this->latestHearbeat?->error_message;
    }
}
