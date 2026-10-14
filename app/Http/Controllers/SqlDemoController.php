<?php

namespace App\Http\Controllers;

use App\Models\SqlProduct;
use App\Support\SampleProducts;

class SqlDemoController extends Controller
{
    /**
     * Same five queries as the MongoDB demo, but against SQLite.
     */
    public function __invoke()
    {
        SqlProduct::query()->truncate();

        foreach (SampleProducts::all() as $product) {
            SqlProduct::create($product);
        }

        $steps = [];

        // 1. Same intent as Product::where('tags', 'swag').
        $steps[] = [
            'label' => 'Products with the "swag" tag',
            'code' => "SqlProduct::whereJsonContains('tags', 'swag')->get()",
            'result' => SqlProduct::whereJsonContains('tags', 'swag')->get()->pluck('name')->all(),
        ];

        // 2. Filter on a date column.
        $steps[] = [
            'label' => 'Products released in the last year',
            'code' => "SqlProduct::where('released_at', '>', now()->subYear())->get()",
            'result' => SqlProduct::where('released_at', '>', now()->subYear())->get()->pluck('name')->all(),
        ];

        // 3. Case-insensitive text search.
        $steps[] = [
            'label' => 'Products whose name matches %shirt%',
            'code' => "SqlProduct::where('name', 'like', '%shirt%')->get()",
            'result' => SqlProduct::where('name', 'like', '%shirt%')->get()->pluck('name')->all(),
        ];

        // 4. Look up a single row by its auto-increment id.
        $first = SqlProduct::first();
        $steps[] = [
            'label' => 'One product by id',
            'code' => 'SqlProduct::find($id)',
            'result' => [$first?->name],
        ];

        // 5. Group by a value nested in a JSON column.
        $stats = SqlProduct::query()
            ->selectRaw('json_extract(details, "$.color") as color, count(*) as count, max(price) as maxPrice')
            ->groupBy('color')
            ->orderBy('color')
            ->get();

        $steps[] = [
            'label' => 'Aggregation: count and max price per color',
            'code' => 'SqlProduct::selectRaw(...)->groupBy(...)->get()',
            'result' => $stats->map(static fn ($row) => [
                'color' => $row->color,
                'count' => (int) $row->count,
                'maxPrice' => (string) $row->maxPrice,
            ])->all(),
        ];

        return view('query-demo', [
            'title' => 'SQL query logging',
            'intro' => 'The same products and the same five queries as the MongoDB page, stored in SQLite. '
                . 'Open Telescope and compare the recorded queries with the MongoDB ones.',
            'steps' => $steps,
            'links' => [
                ['label' => 'Open Telescope Queries', 'url' => url('/telescope/queries')],
                ['label' => 'Compare with MongoDB', 'url' => route('demo')],
            ],
            'rerunUrl' => route('demo.sql'),
        ]);
    }
}
