<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * This table lives on the SQLite connection, next to the MongoDB collection
     * with the same shape. It is used by the SQL comparison demo page.
     */
    public function up(): void
    {
        Schema::connection('sqlite')->create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->timestamp('released_at');
            $table->json('tags');
            $table->json('details');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('sqlite')->dropIfExists('products');
    }
};
