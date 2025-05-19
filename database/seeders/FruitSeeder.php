<?php

namespace Database\Seeders;

use App\Models\Fruit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FruitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Fruit::insert([
            ['id' => 1, 'name' => 'Naga', 'stock_in_kg' => 50, 'price_per_kg' => 40000],
            ['id' => 2, 'name' => 'Apel', 'stock_in_kg' => 20, 'price_per_kg' => 30000],
            ['id' => 3, 'name' => 'Anggur', 'stock_in_kg' => 100, 'price_per_kg' => 50000],
        ]);
    }
}
