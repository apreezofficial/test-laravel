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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
             $table->string('order_number')->unique(); // Order number
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to the user who placed the order
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending'); // Status field 
            $table->decimal('total_amount', 8, 2); // Total amount of the order 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
