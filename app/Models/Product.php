<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function scopeActive($query){
        return $query->where('status',1);
    }

    public function scopeFeatured($query){
        return $query->where('is_featured',1);
    }

    // public function addons()
    // {
    //     return $this->hasMany(ProductAddon::class);
    // }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'product_addons');
    }

    public function variations()
    {
        return $this->belongsToMany(Variation::class, 'product_variations');
    }

    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

}
