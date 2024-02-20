<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Gold Coffee',
                'profit_margin' => 25,
            ],
            [
                'name' => 'Arabic coffee',
                'profit_margin' => 15,
            ],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
