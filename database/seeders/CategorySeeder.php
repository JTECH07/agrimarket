<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Fruits et Légumes',
            'Viandes et Volailles',
            'Produits Laitiers',
            'Céréales et Grains',
            'Plats Préparés',
            'Boissons locales',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category)],
                [
                    'name' => $category,
                    'description' => 'Description pour ' . $category,
                    'is_active' => true,
                ]
            );
        }
    }
}
