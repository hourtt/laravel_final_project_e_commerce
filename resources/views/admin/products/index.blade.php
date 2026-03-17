<x-admin-layout>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- ── Header Row ── --}}
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col xl:flex-row xl:items-center gap-4 justify-between">

            {{-- Title --}}
            <div class="min-w-0 flex-shrink-0">
                <h2 class="text-base font-bold text-gray-900">Products</h2>
                <p class="text-xs text-gray-400 mt-0.5" id="product-count-header">
                    @if ($search !== '')
                        <span id="search-status">
                            {{ $products->total() }} result{{ $products->total() !== 1 ? 's' : '' }} for
                            "<span class="font-semibold text-gray-700">{{ $search }}</span>"
                        </span>
                    @else
                        Manage available products
                    @endif
                </p>
            </div>

            {{-- Simple Search Bar --}}
            <div class="flex-1 w-full max-w-2xl flex items-center gap-2 xl:justify-end">
                <form id="product-search-form" onsubmit="return false;" class="relative flex-1 w-full sm:w-auto">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input id="product-search" type="text" name="search" value="{{ $search }}"
                        placeholder="Search product name…" autocomplete="off"
                        class="w-full pl-9 pr-8 py-2 text-sm border border-gray-200 rounded-lg bg-slate-50
                               focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                               outline-none transition placeholder-gray-400">
                    <button type="button" id="clear-search"
                        class="absolute inset-y-0 right-2 flex items-center text-gray-400 hover:text-gray-600 transition {{ $search === '' ? 'hidden' : '' }}"
                        title="Clear search">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </form>

                {{-- Add Product --}}
                <a href="{{ route('admin.products.create') }}"
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 text-white text-xs font-bold
                           hover:bg-blue-700 transition-colors shadow-sm shadow-blue-500/30 flex-shrink-0 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Add Product
                </a>
            </div>
        </div>

        {{-- ── Category Chips ── --}}
        <div class="px-6 py-4 bg-white border-b border-gray-50 overflow-x-auto no-scrollbar flex items-center gap-2">
            <button type="button" onclick="filterCategory('all')" data-category-id="all"
                class="category-chip whitespace-nowrap px-4 py-2 rounded-full text-xs font-bold transition-all duration-200 flex items-center gap-2
                {{ $categoryId === 'all' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <span>🛍️</span>
                All
            </button>
            @foreach ($categories as $category)
                <button type="button" onclick="filterCategory('{{ $category->id }}')"
                    data-category-id="{{ $category->id }}"
                    class="category-chip whitespace-nowrap px-4 py-2 rounded-full text-xs font-bold transition-all duration-200 flex items-center gap-2
                    {{ $categoryId == $category->id ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    @if ($category->icon && str_contains($category->icon, 'images/category/'))
                        <img src="{{ asset(str_replace('public/', '', $category->icon)) }}" 
                             alt="{{ $category->name }}" 
                             class="w-4 h-4 object-contain"
                             onerror="this.onerror=null; this.parentElement.innerHTML='📁'">
                    @else
                        <span>{{ $category->icon ?? '📁' }}</span>
                    @endif
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        {{-- ── Table ── --}}
        <div class="overflow-x-auto relative" id="products-table-container">
            <div id="loading-overlay"
                class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center hidden">
                <div class="flex flex-col items-center gap-2">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-xs font-medium text-gray-500">Updating...</span>
                </div>
            </div>

            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100">
                        <th class="text-center text-xs font-semibold text-gray-500 tracking-wider px-6 py-3 w-16">
                            Image</th>
                        <th class="text-center text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Product Name</th>
                        <th class="text-center text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Category</th>
                        <th class="text-center text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Price</th>
                        <th class="text-center text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Stock</th>
                        <th class="text-center text-xs font-semibold text-gray-500 tracking-wider px-6 py-3">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="products-tbody">
                    @include('admin.products.partials.product_table', ['products' => $products])
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div
            class="px-6 py-4 bg-slate-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-gray-400" id="pagination-info">
                Showing <span class="font-semibold text-gray-600">{{ $products->count() }}</span>
                product{{ $products->count() !== 1 ? 's' : '' }} of <span
                    class="font-semibold text-gray-600">{{ $products->total() }}</span>
            </div>
            <div id="pagination-links">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            /* Smooth transition for category chips */
            .category-chip {
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Pagination custom styling to match the theme */
            #pagination-links nav div:first-child {
                display: none;
            }

            #pagination-links nav div:last-child {
                margin-top: 0;
            }

            #pagination-links span,
            #pagination-links a {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
                border-radius: 0.5rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            let currentCategoryId = '{{ $categoryId }}';
            let searchTimeout;

            // Handle Search Input
            const searchInput = document.getElementById('product-search');
            const clearBtn = document.getElementById('clear-search');

            searchInput.addEventListener('input', function() {
                const query = this.value.trim();

                if (query.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetchProducts();
                }, 400);
            });

            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                this.classList.add('hidden');
                fetchProducts();
            });

            // Handle Category Filter
            function filterCategory(id) {
                currentCategoryId = id;

                // Update active state of chips
                document.querySelectorAll('.category-chip').forEach(chip => {
                    const chipId = chip.getAttribute('data-category-id');
                    if (chipId === id) {
                        chip.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                        chip.classList.add('bg-blue-600', 'text-white', 'shadow-md');
                    } else {
                        chip.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                        chip.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                    }
                });

                fetchProducts();
            }

            // Handle Pagination AJAX
            document.addEventListener('click', function(e) {
                if (e.target.closest('#pagination-links a')) {
                    e.preventDefault();
                    const url = e.target.closest('a').href;
                    fetchProducts(url);
                }
            });

            function fetchProducts(url = null) {
                const search = searchInput.value.trim();
                const container = document.getElementById('products-tbody');
                const overlay = document.getElementById('loading-overlay');

                overlay.classList.remove('hidden');

                // If no URL provided, build one from current state
                if (!url) {
                    url = new URL('{{ route('admin.products.index') }}');
                    if (search) url.searchParams.set('search', search);
                    if (currentCategoryId !== 'all') url.searchParams.set('category_id', currentCategoryId);
                } else {
                    // If URL provided, ensure category and search are preserved if they are missing
                    const urlObj = new URL(url);
                    if (search && !urlObj.searchParams.has('search')) urlObj.searchParams.set('search', search);
                    if (currentCategoryId !== 'all' && !urlObj.searchParams.has('category_id')) urlObj.searchParams.set(
                        'category_id', currentCategoryId);
                    url = urlObj.toString();
                }

                // Update browser URL without reload
                window.history.pushState({}, '', url);

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        container.innerHTML = data.html;
                        document.getElementById('pagination-links').innerHTML = data.pagination;

                        // Update header and footer counts
                        const countText =
                            `Showing <span class="font-semibold text-gray-600">${data.total > 0 ? document.querySelectorAll('#products-tbody tr').length : 0}</span> product${data.total !== 1 ? 's' : ''} of <span class="font-semibold text-gray-600">${data.total}</span>`;
                        document.getElementById('pagination-info').innerHTML = countText;

                        const headerStatus = document.getElementById('product-count-header');
                        if (search) {
                            headerStatus.innerHTML =
                                `<span>${data.total} result${data.total !== 1 ? 's' : ''} for "<span class="font-semibold text-gray-700">${search}</span>"</span>`;
                        } else {
                            headerStatus.innerHTML = 'Manage available products';
                        }

                        overlay.classList.add('hidden');
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                        overlay.classList.add('hidden');
                    });
            }

            function resetFilters() {
                searchInput.value = '';
                clearBtn.classList.add('hidden');
                filterCategory('all');
            }
        </script>
    @endpush
</x-admin-layout>
