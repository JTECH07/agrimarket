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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('delivery_agent_id')->nullable()->constrained('delivery_agents')->onDelete('set null');
            $table->enum('status', ['pending', 'assigned', 'picked_up', 'in_transit', 'delivered', 'failed'])->default('pending');
            $table->string('tracking_number')->unique();
            $table->text('delivery_notes')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('proof_of_delivery')->nullable(); // URL de la photo ou signature
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
