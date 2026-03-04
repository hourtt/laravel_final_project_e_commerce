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
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight tracking-tight mb-5">
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
            <p class="text-gray-500 mt-2 text-sm">Browse our full collection — search or filter by category.
            </p>
        </div>

        {{-- ──────────────────────────────────────────
             SEARCH BAR
        ────────────────────────────────────────── --}}
        <div class="flex justify-center mb-7">
            <div class="relative w-full max-w-xl group">
                {{-- Search Icon --}}
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg id="search-icon" xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"
                        fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                </div>

                {{-- Input --}}
                <input id="product-search-input" type="text"
                    placeholder="Search products… (e.g. iPhone, Sony, Keyboard)" autocomplete="off"
                    value="{{ $search ?? '' }}"
                    class="w-full pl-12 pr-12 py-3.5 rounded-2xl border border-gray-200 bg-white text-sm text-gray-800 placeholder-gray-400
                           shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500
                           transition-all duration-200" />

                {{-- Clear button (shown only when input has text) --}}
                <button id="search-clear-btn" type="button" onclick="clearSearch()"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-700 transition-colors
                           {{ ($search ?? '') !== '' ? '' : 'hidden' }}"
                    aria-label="Clear search">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- ──────────────────────────────────────────
             CATEGORY FILTER BAR (JS-powered)
        ────────────────────────────────────────── --}}
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
            @foreach ($categories as $cat)
                <button type="button" data-category="{{ $cat }}"
                    onclick="filterProducts('{{ $cat }}')"
                    class="category-btn inline-flex items-center gap-1.5 px-5 py-3
                           rounded-full text-sm font-semibold border transition-all duration-200
                           {{ ($search ?? '') === '' && $category === $cat
                               ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/25'
                               : 'bg-white text-gray-600 border-gray-200 hover:border-blue-400 hover:text-blue-600 hover:shadow-sm' }}">
                    {{ $categoryIcons[$cat] ?? '' }}
                    {{ $cat }}
                </button>
            @endforeach
        </div>

        <!-- Product Grid Container (swapped by JS) -->
        <div id="product-grid-container">
            @include('user.partials.product-grid', [
                'products' => $products,
                'category' => $category,
                'categories' => $categories,
                'search' => $search ?? '',
            ])
        </div>

        <!-- Loading Spinner (hidden by default) -->
        <div id="products-loading" class="hidden flex flex-col items-center justify-center py-24 gap-4">
            <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
            <p class="text-gray-400 text-sm font-medium">Searching…</p>
        </div>

    </section>

    {{-- ============================================================= --}}
    {{-- AJAX CATEGORY FILTER SCRIPT --}}
    {{-- ============================================================= --}}
    <script>
        // ── STATE ─────────────────────────────────────────────────────────────
        let activeCategory = '{{ $category }}';
        let activeSearch = '{{ addslashes($search ?? '') }}';
        let searchDebounce = null;

        // ── HELPERS ───────────────────────────────────────────────────────────

        /** Swap the product grid with a fade transition */
        function swapGrid(url) {
            const container = document.getElementById('product-grid-container');
            container.style.opacity = '0';
            container.style.transition = 'opacity 0.2s';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error(res.status);
                    return res.text();
                })
                .then(html => {
                    container.innerHTML = html;
                    container.style.opacity = '1';
                })
                .catch(err => {
                    console.error('Grid error:', err);
                    container.style.opacity = '1';
                });
        }

        /** Style the category buttons to show which one is active */
        function highlightCategory(category) {
            document.querySelectorAll('.category-btn').forEach(btn => {
                // A button is visually active only when there's no active search
                const isActive = btn.dataset.category === category && activeSearch === '';
                btn.classList.toggle('bg-blue-600', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('border-blue-600', isActive);
                btn.classList.toggle('shadow-lg', isActive);
                btn.classList.toggle('shadow-blue-500\/25', isActive);
                btn.classList.toggle('bg-white', !isActive);
                btn.classList.toggle('text-gray-600', !isActive);
                btn.classList.toggle('border-gray-200', !isActive);
            });
        }

        // ── CATEGORY FILTER ───────────────────────────────────────────────────

        /**
         * filterProducts — fetches the filtered product grid via AJAX
         * without scrolling the page to the top.
         */
        function filterProducts(category) {
            // Clicking a category always clears the search
            activeSearch = '';
            const searchInput = document.getElementById('product-search-input');
            const clearBtn = document.getElementById('search-clear-btn');
            if (searchInput) searchInput.value = '';
            if (clearBtn) clearBtn.classList.add('hidden');

            if (category === activeCategory && activeSearch === '') return;
            activeCategory = category;

            // Update browser URL
            const url = new URL(window.location.href);
            url.searchParams.set('category', category);
            url.searchParams.delete('search');
            history.pushState({
                category
            }, '', url.toString());

            highlightCategory(category);

            swapGrid(`{{ route('products.filter') }}?category=${encodeURIComponent(category)}`);
        }

        // ── SEARCH ────────────────────────────────────────────────────────────

        /**
         * runSearch — immediately fires an AJAX search request.
         * Category highlight is removed while a search is active.
         */
        function runSearch(query) {
            activeSearch = query;
            highlightCategory(activeCategory); // de-highlights all when search is active

            // Update browser URL
            const url = new URL(window.location.href);
            if (query !== '') {
                url.searchParams.set('search', query);
                url.searchParams.delete('category');
            } else {
                url.searchParams.delete('search');
                url.searchParams.set('category', activeCategory);
            }
            history.pushState({
                search: query
            }, '', url.toString());

            if (query === '') {
                // Empty search → revert to category view
                swapGrid(`{{ route('products.filter') }}?category=${encodeURIComponent(activeCategory)}`);
            } else {
                swapGrid(`{{ route('products.search') }}?search=${encodeURIComponent(query)}`);
            }
        }

        /** Clear the search input and revert to the current category */
        function clearSearch() {
            const input = document.getElementById('product-search-input');
            const clearBtn = document.getElementById('search-clear-btn');
            if (input) input.value = '';
            if (clearBtn) clearBtn.classList.add('hidden');
            runSearch('');
        }

        // ── BIND SEARCH INPUT ─────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('product-search-input');
            const clearBtn = document.getElementById('search-clear-btn');

            if (!input) return;

            input.addEventListener('input', () => {
                const val = input.value.trim();

                // Toggle clear-button visibility
                clearBtn?.classList.toggle('hidden', val === '');

                // Debounce: wait 300ms after last keystroke
                clearTimeout(searchDebounce);
                searchDebounce = setTimeout(() => runSearch(val), 300);
            });

            // Escape key clears search
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') clearSearch();
            });
        });

        // ── BROWSER BACK / FORWARD ────────────────────────────────────────────
        window.addEventListener('popstate', () => {
            const params = new URLSearchParams(window.location.search);
            const cat = params.get('category') || 'All';
            const srch = params.get('search') || '';
            const input = document.getElementById('product-search-input');
            const clearBtn = document.getElementById('search-clear-btn');

            if (srch !== '') {
                if (input) input.value = srch;
                if (clearBtn) clearBtn.classList.remove('hidden');
                runSearch(srch);
            } else {
                if (input) input.value = '';
                if (clearBtn) clearBtn.classList.add('hidden');
                if (cat !== activeCategory) filterProducts(cat);
            }
        });
    </script>

</x-user-layout>
