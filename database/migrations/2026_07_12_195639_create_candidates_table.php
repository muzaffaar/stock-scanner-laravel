<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {

            $table->string('ticker')->primary();

            $table->string('minute');

            $table->decimal('price', 12, 4);

            $table->decimal('gap', 8, 2);

            $table->decimal('price_change', 8, 2);

            $table->decimal('ah_change', 8, 2);

            $table->decimal('rvol', 12, 4);

            $table->unsignedBigInteger('float')->nullable();

            $table->boolean('has_news')->default(false);

            $table->string('news_sentiment')->nullable();

            $table->timestamp('news_published_at')->nullable();

            $table->string('news_title')->nullable();

            $table->text('news_url')->nullable();

            $table->timestamp('news_checked_at')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
