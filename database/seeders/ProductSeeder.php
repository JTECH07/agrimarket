<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $producer = User::where('user_type', 'producer')->first();
        $categories = Category::all();

        if (!$producer || $categories->isEmpty()) return;

        Product::create([
            'producer_id' => $producer->id,
            'category_id' => $categories->first()->id,
            'name' => 'Tomates fraîches',
            'description' => 'Belles tomates bien rouges récoltées le matin.',
            'price' => 500,
            'unit' => 'panier',
            'stock_quantity' => 20,
            'is_available' => true,
        ]);

        Product::create([
            'producer_id' => $producer->id,
            'category_id' => $categories->last()->id,
            'name' => 'Sac de Maïs 50kg',
            'description' => 'Maïs blanc de qualité supérieure.',
            'price' => 15000,
            'unit' => 'sac',
            'stock_quantity' => 50,
            'is_available' => true,
        ]);
    }
}
