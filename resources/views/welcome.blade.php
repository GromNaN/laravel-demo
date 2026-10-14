<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MongoDB + Telescope demo</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; margin: 4rem auto; max-width: 40rem; line-height: 1.6; color: #1f2937; }
        a.button { display: inline-block; background: #001e2b; color: #fff; padding: .6rem 1.2rem; border-radius: .375rem; text-decoration: none; margin: 0 .5rem .5rem 0; }
        a.button.alt { background: #00684a; }
    </style>
</head>
<body>
    <h1>Laravel + MongoDB + Telescope</h1>

    <p>Two demo pages run the same five queries, one on MongoDB and one on SQLite, so you can compare how Telescope records each driver.</p>

    <p>
        <a class="button" href="{{ route('demo') }}">MongoDB queries</a>
        <a class="button alt" href="{{ route('demo.sql') }}">SQL queries</a>
        <a class="button alt" href="{{ url('/telescope/queries') }}">Open Telescope Queries</a>
    </p>
</body>
</html>
