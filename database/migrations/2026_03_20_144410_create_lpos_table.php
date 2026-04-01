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
        Schema::create('lpos', function (Blueprint $table) {
            $table->id();
            $table->string('lpo_number')->unique(); // LPO number
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade'); // Link to supplier
            $table->decimal('grand_total', 12, 2)->default(0); // Total value of LPO
            $table->enum('status', ['pending', 'paid'])->default('pending'); // Optional status
            $table->timestamps();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpos');
    }
};
