<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Producer;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin
        User::firstOrCreate(
            ['email' => 'admin@agrimarket.com'],
            [
                'name' => 'Admin Agrimarket',
                'phone' => '0000000000',
                'password' => Hash::make('password123'),
                'user_type' => 'admin',
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        // 2. Customer
        User::firstOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'Client Test',
                'phone' => '22990000001',
                'password' => Hash::make('password123'),
                'user_type' => 'customer',
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        // 3. Producer
        $producerUser = User::firstOrCreate(
            ['email' => 'producer@test.com'],
            [
                'name' => 'Producteur Bio',
                'phone' => '22990000002',
                'password' => Hash::make('password123'),
                'user_type' => 'producer',
                'is_active' => true,
                'is_verified' => true,
            ]
        );
        
        Producer::firstOrCreate(
            ['user_id' => $producerUser->id],
            [
                'farm_name' => 'Ferme Bio Bénin',
                'description' => 'Produits 100% bio de notre ferme',
                'is_verified' => true,
            ]
        );

        // 4. Restaurant
        $restaurantUser = User::firstOrCreate(
            ['email' => 'resto@test.com'],
            [
                'name' => 'Restaurant La Saveur',
                'phone' => '22990000003',
                'password' => Hash::make('password123'),
                'user_type' => 'restaurant',
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        Restaurant::firstOrCreate(
            ['user_id' => $restaurantUser->id],
            [
                'name' => 'Maquis La Saveur',
                'description' => 'Plats locaux et européens',
                'is_open' => true,
            ]
        );

        // 5. Delivery Agent
        User::firstOrCreate(
            ['email' => 'livreur@test.com'],
            [
                'name' => 'Livreur Rapide',
                'phone' => '22990000004',
                'password' => Hash::make('password123'),
                'user_type' => 'delivery_agent',
                'is_active' => true,
                'is_verified' => true,
            ]
        );
    }
}
