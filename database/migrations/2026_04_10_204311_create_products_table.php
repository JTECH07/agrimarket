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
        Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('producer_id')->constrained('producers')->onDelete('cascade');
        $table->foreignId('category_id')->constrained()->onDelete('restrict');
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description');
        $table->decimal('price', 10, 2);
        $table->decimal('discount_price', 10, 2)->nullable();
        $table->string('unit'); // kg, litre, pièce, etc.
        $table->integer('stock_quantity')->default(0);
        $table->integer('min_order_quantity')->default(1);
        $table->boolean('is_available')->default(true);
        $table->boolean('is_organic')->default(false);
        $table->string('origin')->nullable(); // Région de production
        $table->json('certifications')->nullable(); // Bio, Label Rouge, etc.
        $table->timestamps();
        $table->softDeletes();
        
        $table->index(['producer_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
