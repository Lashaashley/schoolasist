<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('supplier_invoices')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->decimal('amount_paid', 15, 2);
            $table->string('payment_method');
            $table->string('payment_reference')->nullable();
            $table->date('payment_date');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['supplier_id', 'invoice_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};

