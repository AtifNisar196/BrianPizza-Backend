<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['session_id'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function addons()
    {
        return $this->hasManyThrough(CartItemAddon::class, CartItem::class, 'cart_id', 'cart_item_id');
    }

    public function variations()
    {
        return $this->hasManyThrough(CartItemVariation::class, CartItem::class, 'cart_id', 'cart_item_id');
    }
}
