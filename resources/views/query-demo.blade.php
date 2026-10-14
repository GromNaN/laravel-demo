<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; margin: 2rem auto; max-width: 60rem; line-height: 1.5; color: #1f2937; }
        code { background: #f3f4f6; padding: .1rem .3rem; border-radius: .25rem; }
        .nav { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.5rem; }
        .step { border: 1px solid #e5e7eb; border-radius: .5rem; padding: 1rem 1.25rem; margin-bottom: 1rem; }
        .step h2 { margin: 0 0 .5rem; font-size: 1.05rem; }
        .query { display: block; background: #111827; color: #f9fafb; padding: .6rem .8rem; border-radius: .375rem; overflow-x: auto; margin: .5rem 0; }
        .result { font-family: ui-monospace, monospace; font-size: .9rem; color: #374151; white-space: pre-wrap; }
        a.button { display: inline-block; background: #001e2b; color: #fff; padding: .5rem 1rem; border-radius: .375rem; text-decoration: none; }
        a.button.alt { background: #00684a; }
    </style>
</head>
<body>
    <div class="nav">
        @foreach ($links as $link)
            <a class="button {{ $loop->first ? '' : 'alt' }}" href="{{ $link['url'] }}">{{ $link['label'] }}</a>
        @endforeach
    </div>

    <h1>{{ $title }}</h1>

    <p>{{ $intro }}</p>

    @foreach ($steps as $step)
        <div class="step">
            <h2>{{ $step['label'] }}</h2>
            <code class="query">{{ $step['code'] }}</code>
            <div class="result">{{ json_encode($step['result'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
        </div>
    @endforeach

    <p><a href="{{ $rerunUrl }}">Run the demo again</a></p>
</body>
</html>
