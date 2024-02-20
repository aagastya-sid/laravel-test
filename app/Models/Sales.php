<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'total',
        'user_id',
        'order_id',
        'unit_cost',
    ];
}
