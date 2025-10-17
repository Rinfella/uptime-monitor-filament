<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'check_interval_minutes',
        'status',
        'last_checked_at',
        'last_up_at',
        'last_down_at',
        'response_time',
        'http_status_code',
        'error_message',
        'notify_on_failure',
        'notify_on_recovery',
        'consecutive_failures',
        'max_consecutive_failures',
        'is_active',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'last_up_at' => 'datetime',
        'last_down_at' => 'datetime',
        'notify_on_failure' => 'boolean',
        'notify_on_recovery' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Determine if the monitor should be checked based on its interval and last checked time
    public function shouldBeChecked(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (is_null($this->last_checked_at)) {
            return true;
        }

        $intervalInMinutes = $this->check_interval_minutes ?? config('services.uptime-monitor.check_interval', 2);

        return $this->last_checked_at->addMinutes($intervalInMinutes)->isPast();
    }

    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'up' => 'success',
            'down' => 'danger',
            'unknown' => 'warning',
            default => 'secondary',
        };
    }

    // Get formatted uptime percentage
    public function getUptimePercentage(): string
    {
        if ($this->status === 'up') {
            return '100%';
        } elseif ($this->status === 'down') {
            return '0%';
        }
        return 'N/A';
    }

    public function getLastCheckedHuman(): string
    {
        if (!$this->last_checked_at) {
            return 'N/A';
        }
        return $this->last_checked_at->diffForHumans();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNeedsCheck($query)
    {
        return $query->active()
            ->where(function ($query) {
                $query->whereNull('last_checked_at')
                    ->orWhereRaw('last_checked_at <= DATE_SUB(NOW(), INTERVAL check_interval_minutes MINUTE)');
            });
    }
}
