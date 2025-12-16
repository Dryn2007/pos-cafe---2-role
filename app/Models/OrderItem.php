<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
