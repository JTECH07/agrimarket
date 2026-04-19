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
        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });

        Schema::table('producers', function (Blueprint $table) {
            $table->string('location')->nullable()->after('farm_name');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('location')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('producers', function (Blueprint $table) {
            $table->dropColumn('location');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
