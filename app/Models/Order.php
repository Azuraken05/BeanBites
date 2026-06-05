<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $fillable = ['total_amount', 'total_items', 'user_id'];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }
}