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
        Schema::create('floats', function (Blueprint $table) {
            $table->id();
            $table->string('ticker', 10)->unique();

            $table->unsignedBigInteger('float');

            $table->decimal('float_percent', 5, 2)->nullable();

            $table->date('effective_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floats');
    }
};
