<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Opsional
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Tambahkan baris ini agar semua kolom bisa diisi
    protected $guarded = [];

    // Relasi ke Item
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
