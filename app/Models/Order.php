<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'store_id', 'external_id', 'customer_name', 'date_created', 'status', 'total', 'raw_data'
    ];
    protected $casts = [
        'raw_data' => 'array',
    ];
}
