<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Kopi Susu Gula Aren', 'price' => 18000, 'category' => 'drink'],
            ['name' => 'Americano', 'price' => 15000, 'category' => 'drink'],
            ['name' => 'Croissant', 'price' => 25000, 'category' => 'food'],
            ['name' => 'Kentang Goreng', 'price' => 12000, 'category' => 'snack'],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}
