<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItemAddon extends Model
{
    use HasFactory;

    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    public function addonCart()
    {
        return $this->belongsTo(ProductAddon::class, 'product_addon_id');
    }
}
