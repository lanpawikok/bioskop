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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('promo_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('rows_of_seats');
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->time('hour');
            $table->dateTime('date');
            $table->boolean('actived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};