<x-user-layout>

    {{-- ============================================================= --}}
    {{-- HERO BANNER --}}
    {{-- ============================================================= --}}
    <section class="relative bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 overflow-hidden">
        <!-- Decorative blobs -->
        <div
            class="absolute top-0 left-0 w-96 h-96 bg-blue-600/20 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-500/20 rounded-full blur-[120px] translate-x-1/2 translate-y-1/2">
        </div>
        <!-- Grid pattern -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: url(\" data:image/svg+xml,%3Csvg
            width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg' %3E%3Cg fill='%23ffffff'
            fill-rule='evenodd' %3E%3Cpath
            d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'
            /%3E%3C/g%3E%3C/svg%3E\");">
        </div>

        <div
            class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 flex flex-col lg:flex-row items-center gap-12">

            <!-- Copy -->
            <div class="lg:w-1/2 text-center lg:text-left">
                <span
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/15 border border-blue-500/30 text-blue-300 text-xs font-semibold mb-6 tracking-wide uppercase">
                    ⚡ New Arrivals 2026
                </span>
                <h1
                    class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight tracking-tight mb-5">
                    Premium Tech,<br>
                    <span class="bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">Unbeatable
                        Prices</span>
                </h1>
                <p class="text-slate-400 text-base md:text-lg mb-8 max-w-md mx-auto lg:mx-0 leading-relaxed">
                    Explore the latest gadgets — phones, laptops, audio, and more. Curated just for you.
                </p>
                <a href="#products-section"
                    class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-blue-600 text-white text-sm font-bold shadow-xl shadow-blue-600/30 hover:bg-blue-500 transition-all duration-300 hover:-translate-y-0.5">
                    Shop Now
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="lg:w-1/2 grid grid-cols-2 gap-4 w-full max-w-sm lg:max-w-none mx-auto">
                @php
                    $stats = [
                        ['value' => '10+', 'label' => 'Products', 'icon' => '📦'],
                        ['value' => '0.1%', 'label' => 'Happy Customers', 'icon' => '😊'],
                        ['value' => '5', 'label' => 'Categories', 'icon' => '🏷️'],
                        ['value' => '24/7', 'label' => 'Support', 'icon' => '🛎️'],
                    ];
                @endphp
                @foreach($stats as $stat)
                    <div
                        class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-sm text-center hover:bg-white/10 transition-colors duration-300">
                        <div class="text-2xl mb-2">{{ $stat['icon'] }}</div>
                        <div class="text-2xl font-extrabold text-white">{{ $stat['value'] }}</div>
                        <div class="text-slate-400 text-xs font-medium mt-0.5">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================= --}}
    {{-- CATEGORY FILTER + PRODUCT GRID --}}
    {{-- ============================================================= --}}
    <section id="products-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

        <!-- Section Header -->
        <div class="mb-8 text-center">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Our Products</h2>
            <p class="text-gray-500 mt-2 text-sm">Browse our full collection — filter by category to find what you need.
            </p>
        </div>

        <!-- Category Filter Bar (JS-powered, no scroll-to-top) -->
        @php
            $categoryIcons = [
                'All' => '🛍️',
                'Phones' => '📱',
                'Computers' => '💻',
                'Audio' => '🎧',
                'Mouse' => '🐁',
                'Keyboards' => '⌨️',
            ];
        @endphp
        <div id="category-filter-bar" class="flex flex-wrap items-center justify-center gap-2 mb-10">
            @foreach($categories as $cat)
                    <button type="button" data-category="{{ $cat }}" onclick="filterProducts('{{ $cat }}')"
                        class="category-btn inline-flex items-center gap-1.5 px-5 py-3
                                                                 rounded-full text-sm font-semibold border transition-all duration-200
                                                                 {{ $category === $cat
                ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/25'
                : 'bg-white text-gray-600 border-gray-200 hover:border-blue-400 hover:text-blue-600 hover:shadow-sm' }}">
                        {{ $categoryIcons[$cat] ?? '' }}
                        {{ $cat }}
                    </button>
            @endforeach
        </div>

        <!-- Product Grid Container (swapped by JS)-->
        <div id="product-grid-container">
            @include('user.partials.product-grid', ['products' => $products, 'category' => $category, 'categories' => $categories])
        </div>

        <!-- Loading Spinner (hidden by default) -->
        <div id="products-loading" class="hidden flex flex-col items-center justify-center py-24 gap-4">
            <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
            <p class="text-gray-400 text-sm font-medium">Loading products…</p>
        </div>

    </section>

    {{-- ============================================================= --}}
    {{-- AJAX CATEGORY FILTER SCRIPT --}}
    {{-- ============================================================= --}}
    <script>
        // Track the currently active category
        let activeCategory = '{{ $category }}';

        /**
         * filterProducts — fetches the product grid partial via AJAX
         * and swaps it in WITHOUT scrolling the page to the top.
         */
        function filterProducts(category) {
            if (category === activeCategory) return; // nothing to do

            activeCategory = category;

            // Update browser URL (for bookmarking/sharing) without triggering navigation
            const url = new URL(window.location.href);
            url.searchParams.set('category', category);
            history.pushState({ category }, '', url.toString());

            // Highlight the selected filter button
            document.querySelectorAll('.category-btn').forEach(btn => {
                const isActive = btn.dataset.category === category;
                btn.classList.toggle('bg-blue-600', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('border-blue-600', isActive);
                btn.classList.toggle('shadow-lg', isActive);
                btn.classList.toggle('shadow-blue-500\\/25', isActive);
                btn.classList.toggle('bg-white', !isActive);
                btn.classList.toggle('text-gray-600', !isActive);
                btn.classList.toggle('border-gray-200', !isActive);
            });

            // Show spinner, hide grid
            const container = document.getElementById('product-grid-container');
            const spinner = document.getElementById('products-loading');
            container.style.opacity = '0';
            container.style.transition = 'opacity 0.2s';

            // Fetch AJAX partial
            fetch(`{{ route('products.filter') }}?category=${encodeURIComponent(category)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(res => {
                    if (!res.ok) throw new Error('Network error: ' + res.status);
                    return res.text();
                })
                .then(html => {
                    container.innerHTML = html;
                    container.style.opacity = '1';
                })
                .catch(err => {
                    console.error('Filter error:', err);
                    container.style.opacity = '1';
                });
        }

        // Handle browser back/forward buttons
        window.addEventListener('popstate', (event) => {
            const params = new URLSearchParams(window.location.search);
            const cat = params.get('category') || 'All';
            if (cat !== activeCategory) {
                filterProducts(cat);
            }
        });
    </script>

</x-user-layout>