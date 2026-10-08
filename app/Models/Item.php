<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'unit', 'stock', 'min_stock'];
    public function usages() { return $this->hasMany(ItemUsage::class); }
    public function getNeedsReorderAttribute(): bool { return $this->stock < $this->min_stock; }
}
