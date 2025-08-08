<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'store_id', 'external_id', 'name', 'sku', 'price', 'image', 'raw_data'
    ];
    protected $casts = [
        'raw_data' => 'array',
    ];
}
