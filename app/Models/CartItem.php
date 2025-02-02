<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function addons()
    {
        return $this->hasMany(CartItemAddon::class);
    }

    public function variations()
    {
        return $this->hasMany(CartItemVariation::class);
    }
}
