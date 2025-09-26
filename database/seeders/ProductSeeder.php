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
        Product::create(['name' => 'Laptop Pro', 'sku' => 'LAP-001', 'price' => 1200.00, 'quantity' => 50, 'image_path' => null]);
        Product::create(['name' => 'Monitor 4K', 'sku' => 'MON-002', 'price' => 450.00, 'quantity' => 20, 'image_path' => null]);
        Product::create(['name' => 'Webcam HD', 'sku' => 'CAM-003', 'price' => 50.00, 'quantity' => 3, 'image_path' => null]); 
        Product::create(['name' => 'Mechanical Keyboard', 'sku' => 'KEY-004', 'price' => 150.00, 'quantity' => 10, 'image_path' => null]);
        Product::create(['name' => 'Wireless Mouse', 'sku' => 'MOU-005', 'price' => 25.00, 'quantity' => 100, 'image_path' => null]);
    }

}
