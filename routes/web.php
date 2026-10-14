<?php

use App\Http\Controllers\DemoController;
use App\Http\Controllers\SqlDemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/demo', DemoController::class)->name('demo');
Route::get('/demo-sql', SqlDemoController::class)->name('demo.sql');
