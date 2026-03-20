<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\Brand;
use \App\Models\Product;
class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'iPhone'],
            ['name' => 'Samsung'],
            ['name' => 'Google Pixel'],
            ['name' => 'OnePlus'],
            ['name' => 'Nothing'],
            ['name' => 'MacBook'],
            ['name' => 'Dell'],
            ['name' => 'HP'],
            ['name' => 'ASUS'],
            ['name' => 'Lenovo'],
            ['name' => 'Sony'],
            ['name' => 'Apple'],
            ['name' => 'Bose'],
            ['name' => 'JBL'],
            ['name' => 'Sennheiser'],
            ['name' => 'Logitech'],
            ['name' => 'Razer'],
            ['name' => 'SteelSeries'],
            ['name' => 'Corsair'],
            ['name' => 'Keychron'],
            ['name' => 'NuPhy'],
        ];

        foreach ($brands as $brandData) {
            $brand = Brand::create($brandData);

            // Simple logic: associate products whose names contain the brand name
            Product::where('name', 'like', '%' . $brand->name . '%')
                ->update(['brand_id' => $brand->id]);
        }

        // Catch leftovers for common brands if not in name
        $samsung = Brand::where('name', 'Samsung')->first();
        if ($samsung) {
             Product::where('name', 'like', '%Galaxy%')->update(['brand_id' => $samsung->id]);
        }
    }
}
