<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductVariation extends Model
{
    use HasFactory;

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function orderVariation()
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }
}
