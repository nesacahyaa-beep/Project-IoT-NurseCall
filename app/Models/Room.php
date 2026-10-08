<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    protected $fillable = ['code', 'name', 'temperature', 'humidity', 'last_seen_at'];

    protected function casts(): array
    {
        return ['last_seen_at' => 'datetime', 'temperature' => 'float', 'humidity' => 'float'];
    }

    public function calls() { return $this->hasMany(Call::class); }
    public function readings() { return $this->hasMany(SensorReading::class); }
    public function activeCall() { return $this->hasOne(Call::class)->active()->latestOfMany(); }

    public function latestReading(): HasOne
    {
        return $this->hasOne(SensorReading::class)->latestOfMany();
    }

    public function getIsOfflineAttribute(): bool
    {
        return ! $this->last_seen_at
            || $this->last_seen_at->lt(now()->subSeconds(config('nursecall.offline_after')));
    }

    // offline | emergency | calling | normal
    public function getDisplayStatusAttribute(): string
    {
        if ($this->is_offline) return 'offline';
        $call = $this->activeCall;
        if (! $call) return 'normal';
        return $call->level === 'emergency' ? 'emergency' : 'calling';
    }

    public function getStatusThemeAttribute(): string
    {
        return ['normal' => 'success', 'calling' => 'warning', 'emergency' => 'danger', 'offline' => 'secondary'][$this->display_status];
    }

    public function getStatusLabelAttribute(): string
    {
        return ['normal' => 'Normal', 'calling' => 'Memanggil', 'emergency' => 'DARURAT', 'offline' => 'Offline'][$this->display_status];
    }

    public function getIsComfortableAttribute(): ?bool
    {
        if ($this->temperature === null || $this->humidity === null) return null;
        [$tMin, $tMax] = config('nursecall.temp_range');
        [$hMin, $hMax] = config('nursecall.humidity_range');
        return $this->temperature >= $tMin && $this->temperature <= $tMax
            && $this->humidity >= $hMin && $this->humidity <= $hMax;
    }

    // Perintah untuk ESP32, dikirim di setiap respons API
    public function deviceCommand(): array
    {
        $this->unsetRelation('activeCall');
        return [
            'buzzer' => $this->activeCall?->status === 'waiting',
            'led'    => match ($this->display_status) {
                'emergency' => 'red',
                'calling'   => 'yellow',
                default     => 'green',
            },
        ];
    }
}