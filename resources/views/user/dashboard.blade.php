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
        <div class="absolute inset-0 opacity-[0.03]"
            style="background-image: url(\" data:image/svg+xml,%3Csvg
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
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-tight mb-5">
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
                        ['value' => '75%', 'label' => 'Happy Customers', 'icon' => '😊'],
                        ['value' => '5', 'label' => 'Categories', 'icon' => '🏷️'],
                        ['value' => '24/7', 'label' => 'Support', 'icon' => '🛎️'],
                    ];
                @endphp
                @foreach ($stats as $stat)
                    <div
                        class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-sm text-center hover:bg-white/10 transition-colors duration-300">
                        <div class="text-2xl mb-2">{{ $stat['icon'] }}</div>
                        <div class="text-2xl font-bold text-white">{{ $stat['value'] }}</div>
                        <div class="text-slate-400 text-xs font-medium mt-0.5">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================= --}}
    {{-- CATEGORY FILTER + PRODUCT GRID (Alpine.js Powered) --}}
    {{-- ============================================================= --}}
    <section id="products-section" 
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14"
        x-data="{
            activeCategory: '{{ $category }}',
            activeSearch: '{{ addslashes($search ?? '') }}',
            isLoading: false,
            loadingCategory: '',
            
            async fetchProducts(url, category = null, search = null) {
                this.isLoading = true;
                if (category !== null) this.loadingCategory = category;
                
                try {
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const html = await response.text();
                    document.getElementById('product-grid-container').innerHTML = html;
                    
                    // Update state
                    if (category !== null) this.activeCategory = category;
                    if (search !== null) this.activeSearch = search;
                    
                    // Update URL
                    const newUrl = new URL(url);
                    newUrl.searchParams.delete('page'); // Reset page on filter/search
                    history.pushState({}, '', newUrl.toString());
                    
                } catch (error) {
                    console.error('Fetch error:', error);
                } finally {
                    this.isLoading = false;
                    this.loadingCategory = '';
                }
            },

            filter(category) {
                this.activeSearch = '';
                const url = `{{ route('home') }}?category=${encodeURIComponent(category)}`;
                this.fetchProducts(url, category, '');
            },

            search(query) {
                this.activeSearch = query;
                const url = `{{ route('home') }}?search=${encodeURIComponent(query)}`;
                this.fetchProducts(url, null, query);
            },

            async handlePagination(event) {
                if (event.target.tagName === 'A' && event.target.href) {
                    event.preventDefault();
                    this.isLoading = true;
                    try {
                        const response = await fetch(event.target.href, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const html = await response.text();
                        document.getElementById('product-grid-container').innerHTML = html;
                        history.pushState({}, '', event.target.href);
                        window.scrollTo({ top: document.getElementById('products-section').offsetTop - 100, behavior: 'smooth' });
                    } catch (error) {
                        console.error('Pagination error:', error);
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }"
        @popstate.window="
            const params = new URLSearchParams(window.location.search);
            const cat = params.get('category') || 'All';
            const srch = params.get('search') || '';
            const page = params.get('page') || 1;
            
            this.activeCategory = cat;
            this.activeSearch = srch;
            
            const url = window.location.href;
            this.isLoading = true;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(h => {
                    document.getElementById('product-grid-container').innerHTML = h;
                })
                .finally(() => this.isLoading = false);
        "
    >

        <style>
            @keyframes fade-up {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-up {
                animation: fade-up 0.5s ease-out forwards;
            }
        </style>

        <!-- Section Header -->
        <div class="mb-8 text-center" :class="{ 'opacity-40 blur-[1px]': isLoading }">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">Our Products</h2>
            <p class="text-gray-500 mt-2 text-sm">Browse our full collection — search or filter by category.</p>
        </div>

        {{-- SEARCH BAR --}}
        <div class="flex justify-center mb-7" :class="{ 'opacity-40 blur-[1px]': isLoading }">
            <div class="relative w-full max-w-xl group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                        fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                </div>

                <input type="text"
                    placeholder="Search products… (e.g. iPhone, Sony, Keyboard)" 
                    autocomplete="off"
                    x-model.debounce.500ms="activeSearch"
                    @input="search(activeSearch)"
                    class="w-full pl-12 pr-12 py-3.5 rounded-2xl border border-gray-200 bg-white text-sm text-gray-800 placeholder-gray-400
                           shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500
                           transition-all duration-200" />

                <button type="button" x-show="activeSearch !== ''" @click="activeSearch = ''; search('')"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-700 transition-colors"
                    aria-label="Clear search">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- CATEGORY FILTER BAR --}}
        <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
            @foreach ($categories as $cat)
                @php
                    $icons = [
                        'All' => '🛍️', 'Phones' => '📱', 'Computers' => '💻', 
                        'Audio' => '🎧', 'Mouse' => '🖱️', 'Keyboards' => '⌨️'
                    ];
                @endphp
                <button type="button" 
                    @click="filter('{{ $cat }}')"
                    class="relative h-[48px] min-w-[120px] inline-flex items-center justify-center gap-1.5 px-6
                           rounded-full text-sm font-semibold border transition-all duration-300
                           active:scale-95 disabled:opacity-70"
                    :class="activeCategory === '{{ $cat }}' && activeSearch === '' 
                           ? 'bg-blue-600 text-white border-blue-600 shadow-xl shadow-blue-500/25' 
                           : 'bg-white text-gray-600 border-gray-200 hover:border-blue-400 hover:text-blue-600'"
                    :disabled="isLoading"
                >
                    <span :class="{ 'opacity-0': isLoading && loadingCategory === '{{ $cat }}' }">
                        {{ $icons[$cat] ?? '📦' }} {{ $cat }}
                    </span>
                    
                    <template x-if="isLoading && loadingCategory === '{{ $cat }}'">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </template>
                </button>
            @endforeach
        </div>

        <!-- Product Grid Container -->
        <div id="product-grid-container" 
            @click="handlePagination($event)"
            class="transition-all duration-500"
            :class="{ 'opacity-40 blur-[1px] pointer-events-none': isLoading }"
        >
            @include('user.partials.product-grid')
        </div>

    </section>
</x-user-layout>
