<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function addons()
    {
        return $this->hasManyThrough(OrderProductAddon::class, OrderProduct::class, 'order_id', 'order_product_id');
    }

    public function variations()
    {
        return $this->hasManyThrough(OrderProductVariation::class, OrderProduct::class, 'order_id', 'order_product_id');
    }
}
