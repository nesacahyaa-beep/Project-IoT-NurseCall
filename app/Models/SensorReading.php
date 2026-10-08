<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    public $timestamps = false;
    protected $fillable = ['room_id', 'temperature', 'humidity', 'recorded_at'];
    protected function casts(): array { return ['recorded_at' => 'datetime']; }
}
