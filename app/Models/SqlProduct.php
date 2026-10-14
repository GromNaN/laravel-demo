<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Same shape as App\Models\Product, but stored in SQLite.
 * Used by the SQL comparison demo page.
 */
class SqlProduct extends Model
{
    protected $connection = 'sqlite';

    protected $table = 'products';

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
            'price' => 'decimal:2',
            'released_at' => 'datetime',
            'tags' => 'array',
            'details' => 'array',
        ];
    }
}
