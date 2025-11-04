<?php

namespace App\Models;

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
        'current_status',
        'notify_on_failure',
        'notify_on_recovery',
        'consecutive_failures',
        'max_consecutive_failures',
        'is_active',
    ];

    public function heartbeats(): HasMany
    {
        return $this->hasMany(Heartbeat::class);
    }

    public function latestHearbeat(): HasOne
    {
        return $this->hasOne(Heartbeat::class)->latestOfMany('checked_at');
    }

    public function getResponseTime(): ?int
    {
        return $this->latestHearbeat?->response_time;
    }

    public function getHttpStatusCode(): ?int
    {
        return $this->latestHearbeat?->http_status_code;
    }

    public function getErrorMessage(): ?string
    {
        return $this->latestHearbeat?->error_message;
    }

    // Determine if the monitor should be checked based on its interval and last checked time
    // public function shouldBeChecked(): bool
    // {
    //     if (!$this->is_active) {
    //         return false;
    //     }
    //
    //     if (is_null($this->last_checked_at)) {
    //         return true;
    //     }
    //
    //     $intervalInMinutes = $this->check_interval_minutes ?? config('services.uptime-monitor.check_interval', 2);
    //
    //     return $this->last_checked_at->addMinutes($intervalInMinutes)->isPast();
    // }
    //
    // public function getStatusBadgeColor(): string
    // {
    //     return match ($this->status) {
    //         'up' => 'success',
    //         'down' => 'danger',
    //         'unknown' => 'warning',
    //         default => 'secondary',
    //     };
    // }

    // Get formatted uptime percentage
    // public function getUptimePercentage(): string
    // {
    //     if ($this->status === 'up') {
    //         return '100%';
    //     } elseif ($this->status === 'down') {
    //         return '0%';
    //     }
    //     return 'N/A';
    // }
    //
    // public function getLastCheckedHuman(): string
    // {
    //     if (!$this->last_checked_at) {
    //         return 'N/A';
    //     }
    //     return $this->last_checked_at->diffForHumans();
    // }
    //
    // public function scopeActive($query)
    // {
    //     return $query->where('is_active', true);
    // }
    //
    // public function scopeNeedsCheck($query)
    // {
    //     return $query->active()
    //         ->where(function ($query) {
    //             $query->whereNull('last_checked_at')
    //                 ->orWhereRaw('last_checked_at <= DATE_SUB(NOW(), INTERVAL check_interval_minutes MINUTE)');
    //         });
    // }
}
