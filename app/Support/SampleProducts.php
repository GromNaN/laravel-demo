<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Shared sample data for the MongoDB and SQL demo pages, so both run the
 * exact same queries on the exact same documents.
 */
class SampleProducts
{
    public static function all(): array
    {
        return [
            [
                'name' => 'MongoDB T-shirt',
                'price' => '29.99',
                'released_at' => Carbon::parse('2024-05-01'),
                'tags' => ['clothing', 'swag'],
                'details' => ['color' => 'green', 'sizes' => ['S', 'M', 'L']],
            ],
            [
                'name' => 'Laravel hoodie',
                'price' => '59.50',
                'released_at' => Carbon::parse('2025-11-15'),
                'tags' => ['clothing', 'swag'],
                'details' => ['color' => 'red', 'sizes' => ['M', 'L', 'XL']],
            ],
            [
                'name' => 'Sticker pack',
                'price' => '4.20',
                'released_at' => Carbon::parse('2026-08-20'),
                'tags' => ['accessory'],
                'details' => ['color' => 'blue'],
            ],
            [
                'name' => 'Mechanical keyboard',
                'price' => '149.00',
                'released_at' => Carbon::parse('2026-01-10'),
                'tags' => ['hardware'],
                'details' => ['color' => 'green', 'layout' => 'AZERTY'],
            ],
        ];
    }
}
