<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;
use App\Models\Menu;
use App\Models\User;
use App\Models\Category;
use App\Models\Restaurant;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $restaurantUser = User::where('user_type', 'restaurant')->first();
        $categories = Category::all();

        if (!$restaurantUser) return;

        $restaurant = Restaurant::firstOrCreate([
            'user_id' => $restaurantUser->id,
        ], [
            'name' => 'Saveurs du Bénin',
            'location' => 'Lomé, Face au grand marché',
        ]);

        $menu = Menu::firstOrCreate([
            'restaurant_id' => $restaurantUser->id,
            'name' => 'Menu Express',
        ]);

        MenuItem::create([
            'menu_id' => $menu->id,
            'category_id' => $categories->where('name', 'Plats Préparés')->first()?->id ?? $categories->first()->id,
            'name' => 'Pâte rouge au poisson',
            'description' => 'Traditionnelle pâte rouge accompagnée de poisson frit et piment.',
            'price' => 1500,
            'is_available' => true,
        ]);

        MenuItem::create([
            'menu_id' => $menu->id,
            'category_id' => $categories->where('name', 'Plats Préparés')->first()?->id ?? $categories->first()->id,
            'name' => 'Riz Gras au Poulet',
            'description' => 'Riz savoureux cuit dans un bouillon de viande avec poulet braisé.',
            'price' => 2500,
            'is_available' => true,
        ]);
    }
}
