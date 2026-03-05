<x-admin-layout>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- ── Header Row: title + search scope + add button ── --}}
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col xl:flex-row xl:items-center gap-4 justify-between">

            {{-- Title --}}
            <div class="min-w-0 flex-shrink-0">
                <h2 class="text-base font-bold text-gray-900">Products</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    @if ($search !== '')
                        {{ $products->count() }} result{{ $products->count() !== 1 ? 's' : '' }} for
                        "<span class="font-semibold text-gray-700">{{ $search }}</span>"
                        <span class="text-gray-300 mx-1">·</span>
                        by <span
                            class="font-semibold text-gray-600">{{ $searchBy === 'category' ? 'Category' : 'Product Name' }}</span>
                    @else
                        Manage available products
                    @endif
                </p>
            </div>

            {{-- Search bar with scope pills --}}
            <form id="product-search-form" method="GET" action="{{ route('admin.products.index') }}"
                class="flex-1 w-full max-w-4xl flex flex-col sm:flex-row flex-wrap lg:flex-nowrap items-center gap-2 xl:justify-end">

                {{-- Hidden scope value — toggled by JS --}}
                <input type="hidden" name="search_by" id="search_by_input" value="{{ $searchBy }}">

                {{-- Segmented scope pills --}}
                <div class="flex items-center bg-slate-100 rounded-lg p-0.5 flex-shrink-0 w-full sm:w-auto overflow-x-auto"
                    role="group" aria-label="Search scope">
                    <button type="button" id="scope-name" onclick="setScope('name')"
                        class="scope-pill flex-1 sm:flex-none px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-150
                               {{ $searchBy !== 'category' ? 'bg-white text-blue-600 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700' }}">
                        Product Name
                    </button>
                    <button type="button" id="scope-category" onclick="setScope('category')"
                        class="scope-pill flex-1 sm:flex-none px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-150
                               {{ $searchBy === 'category' ? 'bg-white text-blue-600 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-700' }}">
                        Category
                    </button>
                </div>

                {{-- Text input + clear ✕ --}}
                <div class="relative flex-1 w-full sm:w-auto min-w-[200px]">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input id="product-search" type="text" name="search" value="{{ $search }}"
                        placeholder="{{ $searchBy === 'category' ? 'Search by category…' : 'Search by product name…' }}"
                        autocomplete="off"
                        class="w-full pl-9 pr-8 py-2 text-sm border border-gray-200 rounded-lg bg-slate-50
                               focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                               outline-none transition placeholder-gray-400">
                    @if ($search !== '')
                        <a href="{{ route('admin.products.index', ['search_by' => $searchBy]) }}"
                            class="absolute inset-y-0 right-2 flex items-center text-gray-400 hover:text-gray-600 transition"
                            title="Clear search">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                </div>

                <button type="submit"
                    class="px-3 py-2 rounded-lg bg-blue-600 text-white text-xs font-semibold
                           hover:bg-blue-700 transition-colors shadow-sm flex-shrink-0 w-full sm:w-auto">
                    Search
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

        {{-- ── Table ── --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500 tracking-wider px-6 py-3 w-16">
                            Image</th>
                        <th class="text-left text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Product Name</th>
                        <th class="text-left text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Category</th>
                        <th class="text-left text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Price</th>
                        <th class="text-left text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Stock</th>
                        <th class="text-right text-xs font-semibold text-gray-500 tracking-wider px-6 py-3">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/60 transition-colors group">

                            {{-- Thumbnail --}}
                            <td class="px-6 py-3">
                                @if ($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                        class="w-11 h-11 object-contain">
                                @else
                                    <div
                                        class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </td>

                            {{-- Name + description snippet --}}
                            <td class="px-4 py-3 max-w-xs align-top">
                                <p class="font-semibold text-gray-800 leading-tight mt-2">{{ $product->name }}</p>
                                <div x-data="{ open: false, uid: {{ $product->id }} }"
                                    @close-descs.window="if ($event.detail.except !== uid) open = false"
                                    class="relative mt-0.5">
                                    <div class="transition-all duration-300 ease-in-out text-[11px] text-gray-400 leading-relaxed overflow-hidden relative"
                                        :style="open ? 'max-height: 20rem;' : 'max-height: 1.4rem;'">
                                        {{ $product->description }}
                                        {{-- Soft gradient fade when collapsed to replace line-clamp; without x-transition so it appears instantly on collapse --}}
                                        <div x-show="!open"
                                            class="absolute bottom-0 left-0 right-0 h-4 bg-gradient-to-t from-white to-transparent pointer-events-none">
                                        </div>
                                    </div>
                                    @if (strlen($product->description) > 60)
                                        <button type="button"
                                            @click.stop="open = !open; if (open) $dispatch('close-descs', { except: uid })"
                                            class="inline-flex items-center gap-0.5 text-[10px] text-blue-500 hover:text-blue-700 font-semibold mt-1 focus:outline-none transition-colors duration-150 relative z-10 w-full bg-white/50 backdrop-blur-sm -top-1 pt-1 rounded">
                                            <span x-text="open ? 'See Less' : 'See More'">See More</span>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="w-3 h-3 transition-transform duration-300"
                                                :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="px-4 py-3">
                                <span class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full text-xs font-semibold">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>

                            {{-- Price --}}
                            <td class="px-4 py-3 text-gray-900">${{ number_format($product->price, 2) }}
                            </td>

                            {{-- Stock --}}
                            <td class="px-4 py-3">
                                @if ($product->stock <= 0)
                                    <span
                                        class="inline-flex items-center gap-1 text-red-600 bg-red-50 px-2 py-0.5 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Out of Stock
                                    </span>
                                @elseif($product->stock <= 5)
                                    <span
                                        class="inline-flex items-center gap-1 text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Low
                                        ({{ $product->stock }})
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full text-xs font-semibold">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $product->stock }}
                                        items
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                        class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors"
                                        title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                        onsubmit="return confirm('Delete {{ addslashes($product->name) }}?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-colors"
                                            title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                @if ($search !== '')
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-40"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-sm font-medium">No products match "<span
                                                class="text-gray-700">{{ $search }}</span>"</p>
                                        <a href="{{ route('admin.products.index') }}"
                                            class="text-xs text-blue-600 font-semibold hover:underline">Clear
                                            search</a>
                                    </div>
                                @else
                                    <p class="text-gray-400 text-sm">No products yet.
                                        <a href="{{ route('admin.products.create') }}"
                                            class="text-blue-600 font-semibold hover:underline">Add one</a>
                                    </p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: total count --}}
        <div class="px-6 py-3 bg-slate-50 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                Showing <span class="font-semibold text-gray-600">{{ $products->count() }}</span>
                product{{ $products->count() !== 1 ? 's' : '' }}
                @if ($search !== '')
                    matching "<span class="font-semibold text-gray-600">{{ $search }}</span>"
                @endif
            </p>
            @if ($search !== '')
                <a href="{{ route('admin.products.index') }}"
                    class="text-xs text-blue-600 font-semibold hover:underline">Show all</a>
            @endif
        </div>
    </div>

    {{-- Auto-submit search on Enter (already a form, but this adds a short debounce on typing) --}}
    @push('scripts')
        <script>
            // ── Scope pill switcher ──────────────────────────────────────────
            function setScope(scope) {
                const input = document.getElementById('search_by_input');
                const searchBox = document.getElementById('product-search');
                const nameBtn = document.getElementById('scope-name');
                const catBtn = document.getElementById('scope-category');

                input.value = scope;

                // Update placeholder text
                searchBox.placeholder = scope === 'category' ?
                    'Search by category…' :
                    'Search by product name…';

                // Swap active / inactive classes on both pills
                const activeClasses = ['bg-white', 'text-blue-600', 'shadow-sm', 'ring-1', 'ring-black/5'];
                const inactiveClasses = ['text-gray-500', 'hover:text-gray-700'];

                function activate(btn) {
                    inactiveClasses.forEach(c => btn.classList.remove(c));
                    activeClasses.forEach(c => btn.classList.add(c));
                }

                function deactivate(btn) {
                    activeClasses.forEach(c => btn.classList.remove(c));
                    inactiveClasses.forEach(c => btn.classList.add(c));
                }

                if (scope === 'category') {
                    deactivate(nameBtn);
                    activate(catBtn);
                } else {
                    activate(nameBtn);
                    deactivate(catBtn);
                }

                // Re-submit if there's already a query; otherwise just focus the input
                if (searchBox.value.trim() !== '') {
                    document.getElementById('product-search-form').submit();
                } else {
                    searchBox.focus();
                }
            }

            // ── 450 ms debounce auto-submit on typing ────────────────────────
            (function() {
                const input = document.getElementById('product-search');
                if (!input) return;
                let timer;
                input.addEventListener('input', function() {
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        document.getElementById('product-search-form').submit();
                    }, 450);
                });
            })();
        </script>
    @endpush
</x-admin-layout>
