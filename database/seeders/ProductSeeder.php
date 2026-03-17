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
            'description' => 'Powered by the blazing-fast A17 Pro chip on a 3nm process, the iPhone 15 Pro delivers console-level performance in a lightweight titanium frame. The 6.1-inch Super Retina XDR ProMotion display stuns with 2000 nits peak brightness and an Always-On experience. A versatile triple-camera system — 48MP Main, 12MP Ultra Wide, and 12MP 3x Telephoto — captures every detail day or night. USB-C with USB 3 speeds and MagSafe make this the most connected iPhone ever.',
            'price' => 999.00,
            'category' => 'Phones',
            'stock' => 25
        ],
        [
            'name' => 'Samsung Galaxy S24',
            'product_image' => 'images/products/phones/samsung-s24.jpg',
            'description' => 'The Galaxy S24 brings the power of Galaxy AI to your pocket, backed by the Snapdragon 8 Gen 3 and 8GB RAM for effortless multitasking. Its 6.2-inch Dynamic AMOLED 2X display at 120Hz stays vivid even in direct sunlight thanks to Vision Booster. A 50MP triple-camera system handles everything from wide landscapes to 3x zoomed portraits with stunning clarity. IP68-rated and built on Android 14 with 7 years of OS support — a phone designed to last.',
            'price' => 849.99,
            'category' => 'Phones',
            'stock' => 15
        ],
        [
            'name' => 'Google Pixel 8',
            'product_image' => 'images/products/phones/google-pixel-8.jpg',
            'description' => 'The Pixel 8 is Google\'s smartest phone yet, powered by the Tensor G3 chip with the Titan M2 security coprocessor for fast, private on-device AI. Its 6.2-inch Actua OLED display runs at a smooth 120Hz with 1400 nits HDR brightness and Always-On support. The 50MP main camera with Night Sight, Real Tone, and Photo Unblur makes every shot look professionally edited. Plus, 7 years of guaranteed OS and security updates means you\'re covered for the long haul.',
            'price' => 699.00,
            'category' => 'Phones',
            'stock' => 10
        ],
        [
            'name' => 'OnePlus 12',
            'product_image' => 'images/products/phones/oneplus-12.jpg',
            'description' => 'The OnePlus 12 is a speed demon — Snapdragon 8 Gen 3, 12GB RAM, and a 5400mAh battery that charges from 0 to 100% in just 24 minutes via 100W SUPERVOOC. Its stunning 6.82-inch QHD+ LTPO OLED display adapts from 1 to 120Hz for silky-smooth scrolling at 4500 nits peak brightness. The Hasselblad-tuned triple camera with a 64MP periscope telephoto delivers pro-grade zoom and LOG video recording. Power, speed, and camera excellence — all in Flowy Emerald.',
            'price' => 799.00,
            'category' => 'Phones',
            'stock' => 8
        ],
        [
            'name' => 'Nothing Phone (2)',
            'product_image' => 'images/products/phones/nothing-phone-2.jpg',
            'description' => 'The Nothing Phone (2) is unlike anything else — its signature transparent back and 33-LED Glyph Interface turn notifications and charging into a visual experience. Snapdragon 8+ Gen 1 with 12GB RAM drives a 6.7-inch LTPO OLED display at 1-120Hz for fluid, responsive interactions. The dual 50MP camera system handles stills and video with ease, backed by a 4700mAh battery with 45W fast charging and wireless charging support. A phone that makes a statement, inside and out.',
            'price' => 599.00,
            'category' => 'Phones',
            'stock' => 20
        ],

        // COMPUTERS
        [
            'name' => 'MacBook Air M3',
            'product_image' => 'images/products/computers/macbook-air-m3.jpg',
            'description' => 'The MacBook Air M3 is the ultimate thin-and-light laptop — featherweight at just 2.7 lbs with up to 18 hours of all-day battery life and zero fan noise. Apple\'s M3 chip with an 8-core CPU and 10-core GPU handles everything from everyday tasks to creative workflows with ease. The gorgeous 13.6-inch Liquid Retina display with P3 wide color and True Tone makes every image pop. Two Thunderbolt 4 ports, Wi-Fi 6E, and support for two external displays round out this powerhouse.',
            'price' => 1099.00,
            'category' => 'Computers',
            'stock' => 12
        ],
        [
            'name' => 'Dell XPS 15',
            'product_image' => 'images/products/computers/dell-xps-15.jpg',
            'description' => 'The Dell XPS 15 is a creative powerhouse built for professionals who demand more. An Intel Core i9-13900H paired with 32GB DDR5 RAM and an RTX 4060 GPU handles video editing, 3D rendering, and gaming without breaking a sweat. The breathtaking 15.6-inch 3.5K OLED display is PANTONE validated with 100% DCI-P3 color accuracy for pixel-perfect creative work. At just 18mm thin and running Windows 11 Pro, this is the premium laptop that does it all.',
            'price' => 2199.00,
            'category' => 'Computers',
            'stock' => 5
        ],
        [
            'name' => 'HP Spectre x360',
            'product_image' => 'images/products/computers/hp-spectre-x360.jpg',
            'description' => 'The HP Spectre x360 is a stunning 2-in-1 that effortlessly transforms between laptop, tent, stand, and tablet modes — perfect for creative and on-the-go professionals. Its 13.5-inch 2.8K OLED touchscreen delivers PANTONE-validated colors on a gorgeous gem-cut aluminum body. Powered by Intel Core i7 with 16GB RAM, it includes an MPP Tilt Pen stored magnetically on the chassis for instant creativity. HP Fast Charge brings it to 50% in just 45 minutes — built for those who never slow down.',
            'price' => 1349.99,
            'category' => 'Computers',
            'stock' => 7
        ],
        [
            'name' => 'ASUS Zephyrus G14',
            'product_image' => 'images/products/computers/asus-zephyrus-g14.jpg',
            'description' => 'The ASUS ROG Zephyrus G14 packs desktop-class gaming performance into a compact 14-inch chassis — AMD Ryzen 9 8945HS and RTX 4060 with ROG\'s liquid metal cooling handle even the most demanding games and creative tasks. The QHD+ OLED display at 120Hz with 100% DCI-P3 color is a visual treat for both gaming and content creation. Its iconic customizable AniMe Matrix lid sets it apart from every other gaming laptop on the market. Power, portability, and personality in one.',
            'price' => 1599.00,
            'category' => 'Computers',
            'stock' => 4
        ],
        [
            'name' => 'Lenovo ThinkPad X1',
            'product_image' => 'images/products/computers/thinkpad-x1.jpg',
            'description' => 'The ThinkPad X1 Carbon Gen 11 is the gold standard for business laptops — MIL-SPEC 810H certified yet weighing just 2.48 lbs, it\'s built to go anywhere without slowing you down. Intel Core i7 vPro with 16GB RAM powers through demanding workloads, backed by up to 15 hours of battery life. Its legendary backlit keyboard with 1.5mm key travel remains the most satisfying typing experience on any laptop. Enterprise-grade security with IR camera, fingerprint reader, and TPM 2.0 keeps your data safe.',
            'price' => 1750.00,
            'category' => 'Computers',
            'stock' => 10
        ],

        // AUDIO
        [
            'name' => 'Sony WH-1000XM5',
            'product_image' => 'images/products/audios/sony-xm5.jpg',
            'description' => 'The Sony WH-1000XM5 is the world\'s best noise-canceling headphone — 8 mics and dual processors deliver adaptive ANC that automatically tunes to your environment. LDAC Hi-Res Audio Wireless and DSEE Extreme upscaling ensure every track sounds its absolute best. Multipoint pairing keeps you connected to two devices at once, while Speak-to-Chat pauses music the moment you start talking. Up to 30 hours of ANC playback, with 3 minutes of charging giving you 3 more hours.',
            'price' => 348.00,
            'category' => 'Audio',
            'stock' => 30
        ],
        [
            'name' => 'Apple AirPods Pro 2',
            'product_image' => 'images/products/audios/airpods-pro-2.jpg',
            'description' => 'AirPods Pro 2 redefine what earbuds can do — Apple\'s H2 chip delivers ANC that\'s 2× stronger than the original, automatically adapting to any environment with Adaptive Audio. Personalized Spatial Audio with dynamic head tracking wraps you in a true 3D soundstage. At up to 6 hours per charge and 30 total with the MagSafe USB-C case, they keep up with your day. IP54-rated and powered by on-device Siri processing, these are earbuds that truly think ahead.',
            'price' => 249.00,
            'category' => 'Audio',
            'stock' => 50
        ],
        [
            'name' => 'Bose QuietComfort',
            'product_image' => 'images/products/audios/bose-qc-ultra.jpg',
            'description' => 'The Bose QuietComfort Ultra Earbuds bring Bose\'s legendary silence together with immersive head-tracked spatial audio for a truly concert-like listening experience. Custom 9.3mm drivers deliver wide-range precision audio, while QuietComfort Mode blocks out the world completely. Angled StayHear Max tips keep them secure all day, and multipoint connection lets you switch between two devices seamlessly. 6 hours per earbud, 24 hours total — IPX4 rated and ready for anything.',
            'price' => 299.00,
            'category' => 'Audio',
            'stock' => 18
        ],
        [
            'name' => 'JBL Flip 6',
            'product_image' => 'images/products/audios/jbl-flip-6.jpg',
            'description' => 'The JBL Flip 6 is the go-anywhere speaker that doesn\'t compromise on sound — a 30W two-way driver system with dedicated tweeter and woofer delivers clear highs and punchy, room-filling bass. Fully IP67 waterproof and dustproof, it can be submerged in 1 meter of water, making it perfect for the pool, beach, or shower. Up to 12 hours of playtime from the 4800mAh battery, rechargeable via USB-C. JBL PartyBoost lets you pair multiple speakers for even bigger sound.',
            'price' => 129.95,
            'category' => 'Audio',
            'stock' => 40
        ],
        [
            'name' => 'Sennheiser HD 660S2',
            'product_image' => 'images/products/audios/sennheiser-hd660.jpg',
            'description' => 'The Sennheiser HD 660S2 is a reference-grade open-back headphone handcrafted in Germany for audiophiles who refuse to settle. Its 300Ω driver covers 8Hz to 41.5kHz with THD below 0.04%, producing a natural, wide soundstage that closed-back headphones simply cannot replicate. Soft velour earpads and a lightweight headband ensure fatigue-free listening for hours on end. Includes both a standard 6.35mm and a 4.4mm balanced Pentaconn cable — ready for high-end amplifiers right out of the box.',
            'price' => 499.00,
            'category' => 'Audio',
            'stock' => 5
        ],

        // MOUSE
        [
            'name' => 'Logitech MX Master 3S',
            'product_image' => 'images/products/mouses/mx-master-3s.jpg',
            'description' => 'The MX Master 3S is the productivity mouse that works as hard as you do — its 8,000 DPI sensor glides flawlessly even on glass, while the MagSpeed electromagnetic scroll wheel flies through 1,000 lines in under a second. Primary clicks are 90% quieter, keeping your workspace calm and focused. Logi Options+ enables deep per-app button customization, and Logitech Flow lets you control up to 3 computers at once. USB-C rechargeable with up to 70 days per charge.',
            'price' => 99.00,
            'category' => 'Mouse',
            'stock' => 22
        ],
        [
            'name' => 'Razer DeathAdder V3',
            'product_image' => 'images/products/mouses/razer-deathadder-v3.jpg',
            'description' => 'At just 59g, the Razer DeathAdder V3 is the lightest DeathAdder ever built — a razor-sharp 30,000 DPI Focus Pro sensor tracks with pixel-perfect accuracy at 750 IPS. Gen-3 optical switches fire at the speed of light with zero debounce delay and a 90 million click lifespan. A glass fiber-reinforced shell keeps it rigid without adding weight, while 100% PTFE mouse feet glide effortlessly across any surface. The iconic ergonomic shape suits palm, claw, and fingertip grips alike.',
            'price' => 69.99,
            'category' => 'Mouse',
            'stock' => 35
        ],
        [
            'name' => 'SteelSeries Rival 3',
            'product_image' => 'images/products/mouses/steelseries-rival-3.jpg',
            'description' => 'The SteelSeries Rival 3 Wireless delivers high-performance gaming without the cable — its TrueMove Air sensor tracks up to 18,000 CPI with precise lift-off detection. Dual wireless modes offer sub-1ms 2.4GHz for competition or Bluetooth 5.0 for everyday use. Six programmable buttons in an ambidextrous layout work equally well for right and left-handed players. The 400+ hour battery life is simply unmatched — charge once, game for weeks.',
            'price' => 49.00,
            'category' => 'Mouse',
            'stock' => 15
        ],
        [
            'name' => 'Apple Magic Mouse',
            'product_image' => 'images/products/mouses/apple-magic-mouse.jpg',
            'description' => 'The Apple Magic Mouse transforms your entire top surface into a Multi-Touch gesture pad — swipe, scroll, and switch between apps with natural, intuitive movements and no scroll wheel required. It pairs instantly via iCloud across all your Apple devices with Bluetooth 5.0, and its laser sensor tracks reliably on virtually any surface. A full charge on the Lightning cable delivers over a month of use. At just 99g with a 21.6mm profile, it\'s as minimal and elegant as Apple design gets.',
            'price' => 79.00,
            'category' => 'Mouse',
            'stock' => 20
        ],
        [
            'name' => 'Corsair Scimitar RGB',
            'product_image' => 'images/products/mouses/corsair-scimitar.jpg',
            'description' => 'The Corsair Scimitar RGB Elite was designed for MMO and MOBA players who need every ability instantly accessible — 15 mechanical side buttons in a 3×5 grid put your entire skill bar at your thumb. The exclusive Key Slider shifts the button panel up to 8mm forward or backward for a perfect fit with any hand size. An 18,000 DPI optical sensor with 1,000Hz polling ensures pinpoint accuracy in every fight. Fully programmable with multi-zone RGB through Corsair iCUE software.',
            'price' => 79.99,
            'category' => 'Mouse',
            'stock' => 12
        ],

        // KEYBOARDS
        [
            'name' => 'Keychron K2 V2',
            'product_image' => 'images/products/keyboards/keychron-k2.jpg',
            'description' => 'The Keychron K2 V2 is the ideal everyday mechanical keyboard — a compact 75% layout keeps your function row and arrow keys without the bulk of a full-size board. Gateron Blue switches deliver satisfying tactile clicks beloved by typists, with per-key RGB and 18 lighting effects that look great on any desk. Triple connectivity via Bluetooth 5.1 (3 devices) or USB-C wired, with Mac and Windows toggle built-in. The 4000mAh battery lasts up to 240 hours without backlight.',
            'price' => 79.00,
            'category' => 'Keyboards',
            'stock' => 14
        ],
        [
            'name' => 'Logitech G915 TKL',
            'product_image' => 'images/products/keyboards/logitech-g915.jpg',
            'description' => 'The Logitech G915 TKL brings ultra-thin low-profile GL switches to wireless gaming, with LIGHTSPEED technology delivering sub-1ms latency indistinguishable from wired. Clicky GL switches actuate at 1.5mm for faster, lighter keystrokes, all on an ultra-slim aircraft-grade aluminum chassis that looks as premium as it performs. Per-key LIGHTSYNC RGB with 16.8 million colors is programmable via G HUB. Up to 40 hours with RGB, 135 hours without — no compromise on battery life.',
            'price' => 229.99,
            'category' => 'Keyboards',
            'stock' => 9
        ],
        [
            'name' => 'SteelSeries Apex Pro',
            'product_image' => 'images/products/keyboards/steelseries-apex-pro.jpg',
            'description' => 'The SteelSeries Apex Pro is the world\'s first keyboard with per-key adjustable actuation — OmniPoint 2.0 magnetic switches let you set each key from 0.1mm to 4.0mm, so gaming keys are hair-trigger fast while typing keys feel natural and comfortable. A built-in OLED Smart Display shows real-time stats and settings at a glance without alt-tabbing. Per-key PrismSync RGB, a dedicated volume dial, USB 3.1 passthrough, and an aluminum top frame complete this flagship-class board.',
            'price' => 199.00,
            'category' => 'Keyboards',
            'stock' => 6
        ],
        [
            'name' => 'NuPhy Air75',
            'product_image' => 'images/products/keyboards/nuphy-air75.jpg',
            'description' => 'The NuPhy Air75 is your perfect travel companion — a 75% low-profile mechanical keyboard that\'s lightweight, compact, and surprisingly satisfying to type on. Gateron KS-33 switches with 1.5mm actuation feel incredibly responsive while keeping the profile ultra-slim. Triple connectivity — 2.4GHz dongle, Bluetooth 5.0, or USB-C wired — gives you total flexibility across devices. At 550g with a 3000mAh battery lasting up to 200 hours, it\'s built to take anywhere.',
            'price' => 129.00,
            'category' => 'Keyboards',
            'stock' => 11
        ],
        [
            'name' => 'Razer Huntsman Mini',
            'product_image' => 'images/products/keyboards/razer-huntsman-mini.jpg',
            'description' => 'The Razer Huntsman Mini is the fastest 60% keyboard ever — Linear Optical switches actuate at exactly 1.0mm via infrared light for true zero-delay input at the speed of light. Its compact footprint maximizes desk space without losing any functionality, thanks to programmable Fn layers and Razer Synapse 3 remapping. Doubleshot PBT keycaps resist shine and fading for a premium feel that lasts for years. Per-key Chroma RGB with game-reactive lighting for hundreds of title makes it as beautiful as it is fast.',
            'price' => 119.99,
            'category' => 'Keyboards',
            'stock' => 25
        ],
    ];

    // Seed simple categories
    $icons = [
        'Phones'    => 'public/images/category/cat_smartphone.png',
        'Computers' => 'public/images/category/cat_laptop.png',
        'Audio'     => 'public/images/category/cat_headphones.png',
        'Mouse'     => 'public/images/category/cat_mouse.png',
        'Keyboards' => 'public/images/category/cat_keyboard.png',
    ];

    $categoryMap = [];
    foreach ($icons as $name => $icon) {
        $cat = \App\Models\Category::updateOrCreate(['name' => $name], ['icon' => $icon]);
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
