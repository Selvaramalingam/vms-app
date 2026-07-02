<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'login_datetime' => 'datetime',
        'logout_datetime' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parse user agent string into a readable device name.
     */
    public function getDeviceNameAttribute(): string
    {
        $ua = $this->device ?? '';
        if (empty($ua)) return 'Unknown Device';

        // Detect platform / device
        $platform = 'Unknown';
        if (preg_match('/iPhone/i', $ua)) {
            $platform = 'iPhone';
        } elseif (preg_match('/iPad/i', $ua)) {
            $platform = 'iPad';
        } elseif (preg_match('/Android.*Mobile/i', $ua)) {
            $platform = 'Android Phone';
        } elseif (preg_match('/Android/i', $ua)) {
            $platform = 'Android Tablet';
        } elseif (preg_match('/Macintosh|Mac OS X/i', $ua)) {
            $platform = 'MacBook';
        } elseif (preg_match('/Windows/i', $ua)) {
            $platform = 'Windows PC';
        } elseif (preg_match('/Linux/i', $ua)) {
            $platform = 'Linux PC';
        }

        // Detect browser
        $browser = 'Unknown';
        if (preg_match('/Edg\//i', $ua)) {
            $browser = 'Edge';
        } elseif (preg_match('/OPR|Opera/i', $ua)) {
            $browser = 'Opera';
        } elseif (preg_match('/Chrome/i', $ua) && !preg_match('/Edg/i', $ua)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua)) {
            $browser = 'Safari';
        } elseif (preg_match('/Firefox/i', $ua)) {
            $browser = 'Firefox';
        }

        return "{$platform} ({$browser})";
    }

    /**
     * Get the platform type: mobile or desktop.
     */
    public function getPlatformTypeAttribute(): string
    {
        $ua = $this->device ?? '';
        if (preg_match('/iPhone|Android.*Mobile|iPod|Windows Phone/i', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Get the platform name (OS).
     */
    public function getPlatformAttribute(): string
    {
        $ua = $this->device ?? '';
        if (preg_match('/iPhone|iPad|Macintosh/i', $ua)) return 'macOS / iOS';
        if (preg_match('/Android/i', $ua)) return 'Android';
        if (preg_match('/Windows/i', $ua)) return 'Windows';
        if (preg_match('/Linux/i', $ua)) return 'Linux';
        return 'Unknown';
    }

    /**
     * Get the browser name.
     */
    public function getBrowserNameAttribute(): string
    {
        $ua = $this->device ?? '';
        if (preg_match('/Edg\//i', $ua)) return 'Edge';
        if (preg_match('/OPR|Opera/i', $ua)) return 'Opera';
        if (preg_match('/Chrome/i', $ua) && !preg_match('/Edg/i', $ua)) return 'Chrome';
        if (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua)) return 'Safari';
        if (preg_match('/Firefox/i', $ua)) return 'Firefox';
        return 'Unknown';
    }

    /**
     * Check if the session is currently active.
     */
    public function getIsActiveAttribute(): bool
    {
        return is_null($this->logout_datetime);
    }
}
