<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Call extends Model
{
    protected $fillable = ['room_id', 'level', 'status', 'called_at', 'accepted_at',
                           'completed_at', 'response_seconds', 'accepted_by'];

    protected function casts(): array
    {
        return ['called_at' => 'datetime', 'accepted_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function room() { return $this->belongsTo(Room::class); }
    public function usages() { return $this->hasMany(ItemUsage::class); }
    public function scopeActive($q) { return $q->whereIn('status', ['waiting', 'accepted']); }
}
