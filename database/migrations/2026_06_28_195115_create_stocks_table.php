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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('ticker', 10);
            $table->date('trading_date');

            // Prices
            $table->decimal('pm_open', 10, 4)->nullable();
            $table->decimal('rth_open', 10, 4)->nullable();
            $table->decimal('high', 10, 4)->nullable();
            $table->decimal('low', 10, 4)->nullable();
            $table->decimal('rth_close', 10, 4)->nullable();
            $table->decimal('ah_close', 10, 4)->nullable();
            $table->decimal('vwap', 10, 4)->nullable();

            // Volume
            $table->decimal('volume', 24, 8)->nullable();
            $table->decimal('adv20', 24, 8)->nullable();
            $table->decimal('adv50', 24, 8)->nullable();
            $table->decimal('adv90', 24, 8)->nullable();

            // Float
            $table->unsignedBigInteger('float')->nullable();
            $table->decimal('float_percent', 5, 2)->nullable();

            $table->unsignedInteger('transactions')->nullable();

            $table->timestamps();

            $table->unique(['ticker']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
