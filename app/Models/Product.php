<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'products';

    protected $fillable = [
        'name',
        'price',
        'released_at',
        'tags',
        'details',
    ];

    protected function casts(): array
    {
        return [
            // Stored as BSON Decimal128, shows as {"$numberDecimal": "..."} in the recorded command.
            'price' => 'decimal:2',
            // Stored as a BSON date, shows as {"$date": {"$numberLong": "..."}}.
            'released_at' => 'datetime',
            // Arrays and nested documents need no cast: MongoDB stores them natively.
        ];
    }
}
