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
        // PHONES
        [
            'name' => 'iPhone 15 Pro',
            'product_image' => 'images/products/phones/iphone-15-pro.jpg',
            'description' => '128GB, Natural Titanium, A17 Pro chip.',
            'price' => 999.00,
            'category' => 'Phones',
            'stock' => 25
        ],
        [
            'name' => 'Samsung Galaxy S24',
            'product_image' => 'images/products/phones/samsung-s24.jpg',
            'description' => '256GB, Phantom Black, AI Powered Features.',
            'price' => 849.99,
            'category' => 'Phones',
            'stock' => 15
        ],
        [
            'name' => 'Google Pixel 8',
            'product_image' => 'images/products/phones/google-pixel-8.jpg',
            'description' => '128GB, Obsidian, Best-in-class Camera.',
            'price' => 699.00,
            'category' => 'Phones',
            'stock' => 10
        ],
        [
            'name' => 'OnePlus 12',
            'product_image' => 'images/products/phones/oneplus-12.jpg',
            'description' => '512GB, Flowy Emerald, 100W Fast Charging.',
            'price' => 799.00,
            'category' => 'Phones',
            'stock' => 8
        ],
        [
            'name' => 'Nothing Phone (2)',
            'product_image' => 'images/products/phones/nothing-phone-2.jpg',
            'description' => '256GB, Dark Grey, Unique Glyph Interface.',
            'price' => 599.00,
            'category' => 'Phones',
            'stock' => 20
        ],

        // COMPUTERS
        [
            'name' => 'MacBook Air M3',
            'product_image' => 'images/products/computers/macbook-air-m3.jpg',
            'description' => '13-inch, 8GB RAM, 256GB SSD, Midnight.',
            'price' => 1099.00,
            'category' => 'Computers',
            'stock' => 12
        ],
        [
            'name' => 'Dell XPS 15',
            'product_image' => 'images/products/computers/dell-xps-15.jpg',
            'description' => 'Intel i9, 32GB RAM, 1TB SSD, OLED Touch.',
            'price' => 2199.00,
            'category' => 'Computers',
            'stock' => 5
        ],
        [
            'name' => 'HP Spectre x360',
            'product_image' => 'images/products/computers/hp-spectre-x360.jpg',
            'description' => '2-in-1 Laptop, Intel i7, 16GB RAM, Pen Included.',
            'price' => 1349.99,
            'category' => 'Computers',
            'stock' => 7
        ],
        [
            'name' => 'ASUS Zephyrus G14',
            'product_image' => 'images/products/computers/asus-zephyrus-g14.jpg',
            'description' => 'Gaming Laptop, RTX 4060, 120Hz Display.',
            'price' => 1599.00,
            'category' => 'Computers',
            'stock' => 4
        ],
        [
            'name' => 'Lenovo ThinkPad X1',
            'product_image' => 'images/products/computers/thinkpad-x1.jpg',
            'description' => 'Carbon Gen 11, Ultralight Business Laptop.',
            'price' => 1750.00,
            'category' => 'Computers',
            'stock' => 10
        ],

        // AUDIO
        [
            'name' => 'Sony WH-1000XM5',
            'product_image' => 'images/products/audios/sony-xm5.jpg',
            'description' => 'Wireless Noise Canceling Over-Ear Headphones.',
            'price' => 348.00,
            'category' => 'Audio',
            'stock' => 30
        ],
        [
            'name' => 'Apple AirPods Pro 2',
            'product_image' => 'images/products/audios/airpods-pro-2.jpg',
            'description' => 'MagSafe Case (USB-C), Active Noise Cancellation.',
            'price' => 249.00,
            'category' => 'Audio',
            'stock' => 50
        ],
        [
            'name' => 'Bose QuietComfort',
            'product_image' => 'images/products/audios/bose-qc-ultra.jpg',
            'description' => 'Ultra Earbuds, World-class noise cancellation.',
            'price' => 299.00,
            'category' => 'Audio',
            'stock' => 18
        ],
        [
            'name' => 'JBL Flip 6',
            'product_image' => 'images/products/audios/jbl-flip-6.jpg',
            'description' => 'Portable Waterproof Bluetooth Speaker, Blue.',
            'price' => 129.95,
            'category' => 'Audio',
            'stock' => 40
        ],
        [
            'name' => 'Sennheiser HD 660S2',
            'product_image' => 'images/products/audios/sennheiser-hd660.jpg',
            'description' => 'Audiophile Hi-Res Open-Back Headphones.',
            'price' => 499.00,
            'category' => 'Audio',
            'stock' => 5
        ],

        // MOUSE
        [
            'name' => 'Logitech MX Master 3S',
            'product_image' => 'images/products/mouses/mx-master-3s.jpg',
            'description' => 'Ergonomic Wireless Mouse, Silent Clicks.',
            'price' => 99.00,
            'category' => 'Mouse',
            'stock' => 22
        ],
        [
            'name' => 'Razer DeathAdder V3',
            'product_image' => 'images/products/mouses/razer-deathadder-v3.jpg',
            'description' => 'Ultra-lightweight Wired Gaming Mouse.',
            'price' => 69.99,
            'category' => 'Mouse',
            'stock' => 35
        ],
        [
            'name' => 'SteelSeries Rival 3',
            'product_image' => 'images/products/mouses/steelseries-rival-3.jpg',
            'description' => 'Wireless Gaming Mouse, 400+ Hour Battery.',
            'price' => 49.00,
            'category' => 'Mouse',
            'stock' => 15
        ],
        [
            'name' => 'Apple Magic Mouse',
            'product_image' => 'images/products/mouses/apple-magic-mouse.jpg',
            'description' => 'Multi-Touch Surface, Rechargeable, White.',
            'price' => 79.00,
            'category' => 'Mouse',
            'stock' => 20
        ],
        [
            'name' => 'Corsair Scimitar RGB',
            'product_image' => 'images/products/mouses/corsair-scimitar.jpg',
            'description' => 'Elite MMO Gaming Mouse, 15 Programmable Buttons.',
            'price' => 79.99,
            'category' => 'Mouse',
            'stock' => 12
        ],

        // KEYBOARDS
        [
            'name' => 'Keychron K2 V2',
            'product_image' => 'images/products/keyboards/keychron-k2.jpg',
            'description' => 'Wireless Mechanical Keyboard, Gateron Blue.',
            'price' => 79.00,
            'category' => 'Keyboards',
            'stock' => 14
        ],
        [
            'name' => 'Logitech G915 TKL',
            'product_image' => 'images/products/keyboards/logitech-g915.jpg',
            'description' => 'Tenkeyless Lightspeed Wireless RGB Mechanical.',
            'price' => 229.99,
            'category' => 'Keyboards',
            'stock' => 9
        ],
        [
            'name' => 'SteelSeries Apex Pro',
            'product_image' => 'images/products/keyboards/steelseries-apex-pro.jpg',
            'description' => 'Adjustable OmniPoint Switches, RGB Backlit.',
            'price' => 199.00,
            'category' => 'Keyboards',
            'stock' => 6
        ],
        [
            'name' => 'NuPhy Air75',
            'product_image' => 'images/products/keyboards/nuphy-air75.jpg',
            'description' => 'Low-Profile Wireless Mechanical Keyboard.',
            'price' => 129.00,
            'category' => 'Keyboards',
            'stock' => 11
        ],
        [
            'name' => 'Razer Huntsman Mini',
            'product_image' => 'images/products/keyboards/razer-huntsman-mini.jpg',
            'description' => '60% Optical Gaming Keyboard, Clicky Purple.',
            'price' => 119.99,
            'category' => 'Keyboards',
            'stock' => 25
        ],
    ];

    // Seed simple categories
    $icons = [
        'Phones'    => '📱',
        'Computers' => '💻',
        'Audio'     => '🎧',
        'Mouse'     => '🖱️',
        'Keyboards' => '⌨️',
    ];

    $categoryMap = [];
    foreach ($icons as $name => $icon) {
        $cat = \App\Models\Category::firstOrCreate(['name' => $name], ['icon' => $icon]);
        $categoryMap[$name] = $cat->id;
    }

    foreach ($products as $product) {
        $categoryId = $categoryMap[$product['category']] ?? null;
        unset($product['category']);
        $product['category_id'] = $categoryId;
        
        Product::create($product);
    }
}
}
