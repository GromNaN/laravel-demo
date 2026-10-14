<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\SampleProducts;

class DemoController extends Controller
{
    /**
     * Run a set of representative MongoDB queries so they show up in Telescope.
     */
    public function __invoke()
    {
        // Reset the collection so the demo is repeatable.
        Product::truncate();

        foreach (SampleProducts::all() as $product) {
            Product::create($product);
        }

        $steps = [];

        // 1. Simple find with a filter on an array field.
        $steps[] = [
            'label' => 'Products with the "swag" tag',
            'code' => "Product::where('tags', 'swag')->get()",
            'result' => Product::where('tags', 'swag')->get()->pluck('name')->all(),
        ];

        // 2. Filter on a BSON date. The query shows a {"$date": ...} value.
        $steps[] = [
            'label' => 'Products released in the last year',
            'code' => "Product::where('released_at', '>', now()->subYear())->get()",
            'result' => Product::where('released_at', '>', now()->subYear())->get()->pluck('name')->all(),
        ];

        // 3. Regex search. The query shows a {"$regularExpression": ...} value.
        $steps[] = [
            'label' => 'Products whose name matches /shirt/i',
            'code' => "Product::where('name', 'regex', '/shirt/i')->get()",
            'result' => Product::where('name', 'regex', '/shirt/i')->get()->pluck('name')->all(),
        ];

        // 4. Look up a single document by its ObjectId.
        $first = Product::first();
        $steps[] = [
            'label' => 'One product by _id',
            'code' => 'Product::find($id)',
            'result' => [$first?->name],
        ];

        // 5. Aggregation pipeline with a nested group.
        $stats = Product::raw(fn ($collection) => $collection->aggregate([
            ['$group' => [
                '_id' => '$details.color',
                'count' => ['$sum' => 1],
                'maxPrice' => ['$max' => '$price'],
            ]],
            ['$sort' => ['_id' => 1]],
        ])->toArray());

        $steps[] = [
            'label' => 'Aggregation: count and max price per color',
            'code' => "Product::raw(fn (\$c) => \$c->aggregate([...]))",
            'result' => array_map(static fn ($row) => [
                'color' => $row->_id,
                'count' => $row->count,
                'maxPrice' => (string) $row->maxPrice,
            ], $stats),
        ];

        return view('query-demo', [
            'title' => 'MongoDB query logging',
            'intro' => 'The same products and the same five queries as the SQL page, stored in MongoDB. '
                . 'Open Telescope to see how each command is recorded, including ObjectId, dates, '
                . 'Decimal128, regex and the aggregation pipeline.',
            'steps' => $steps,
            'links' => [
                ['label' => 'Open Telescope Queries', 'url' => url('/telescope/queries')],
                ['label' => 'Compare with SQL', 'url' => route('demo.sql')],
            ],
            'rerunUrl' => route('demo'),
        ]);
    }
}
