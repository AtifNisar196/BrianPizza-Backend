<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductAddon extends Model
{
    use HasFactory;

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function addonCart()
    {
        return $this->belongsTo(ProductAddon::class, 'addon_id');
    }
}
