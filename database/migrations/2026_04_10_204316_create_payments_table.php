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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('reference')->unique(); // Ex: Référence FedaPay
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['mobile_money', 'card', 'cash_on_delivery', 'wallet']);
            $table->enum('status', ['pending', 'successful', 'failed', 'refunded'])->default('pending');
            $table->string('gateway')->nullable(); // Ex: fedapay
            $table->json('gateway_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
