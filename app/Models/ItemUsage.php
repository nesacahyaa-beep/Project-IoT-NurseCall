<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemUsage extends Model
{
    protected $fillable = ['item_id', 'call_id', 'type', 'quantity', 'note'];
    public function item() { return $this->belongsTo(Item::class); }
    public function call() { return $this->belongsTo(Call::class); }
}
