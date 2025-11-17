<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@ecom.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Client
        $client = User::create([
            'name' => 'Client Test',
            'email' => 'client@ecom.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'phone' => '0612345678',
            'address' => '123 Rue Test, Paris',
        ]);

        Cart::create(['user_id' => $client->id]);

        // Create Categories
        $electronics = Category::create([
            'name' => 'Électronique',
            'slug' => 'electronique',
            'description' => 'Produits électroniques',
        ]);

        $clothing = Category::create([
            'name' => 'Vêtements',
            'slug' => 'vetements',
            'description' => 'Vêtements et accessoires',
        ]);

        $home = Category::create([
            'name' => 'Maison',
            'slug' => 'maison',
            'description' => 'Articles pour la maison',
        ]);

        // Create Products
        Product::create([
            'name' => 'Smartphone XYZ',
            'slug' => 'smartphone-xyz',
            'sku' => 'PHONE-001',
            'description' => 'Smartphone dernière génération avec écran OLED',
            'price' => 599.99,
            'stock' => 50,
            'category_id' => $electronics->id,
        ]);

        Product::create([
            'name' => 'Laptop Pro',
            'slug' => 'laptop-pro',
            'sku' => 'LAP-001',
            'description' => 'Ordinateur portable haute performance',
            'price' => 1299.99,
            'stock' => 30,
            'category_id' => $electronics->id,
        ]);

        Product::create([
            'name' => 'T-Shirt Premium',
            'slug' => 't-shirt-premium',
            'sku' => 'TSHIRT-001',
            'description' => 'T-shirt en coton bio',
            'price' => 29.99,
            'stock' => 100,
            'category_id' => $clothing->id,
        ]);

        Product::create([
            'name' => 'Canapé Confort',
            'slug' => 'canape-confort',
            'sku' => 'SOFA-001',
            'description' => 'Canapé 3 places ultra confortable',
            'price' => 899.99,
            'stock' => 15,
            'category_id' => $home->id,
        ]);

        Product::create([
            'name' => 'Écouteurs Bluetooth',
            'slug' => 'ecouteurs-bluetooth',
            'sku' => 'EAR-001',
            'description' => 'Écouteurs sans fil avec réduction de bruit',
            'price' => 149.99,
            'stock' => 75,
            'category_id' => $electronics->id,
        ]);
    }
}
