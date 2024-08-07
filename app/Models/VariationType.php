<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationType extends Model
{
    use HasFactory;

    public function variations(){
        return $this->hasMany(Variation::class)->where('status', 1);
    }

    public function scopeActive($query){
        return $query->where('status',1);
    }
}
