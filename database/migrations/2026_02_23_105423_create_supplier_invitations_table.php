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
        Schema::create('supplier_invitations', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('invoice_id')->nullable();

            
            $table->string('category')->nullable();
            $table->text('message')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('responded')->default(false);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invitations');
    }
};
