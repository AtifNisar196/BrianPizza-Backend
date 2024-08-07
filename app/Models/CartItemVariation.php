<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItemVariation extends Model
{
    use HasFactory;

    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    public function variationCart()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}
