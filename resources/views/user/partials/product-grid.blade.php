@php
    $categoryIcons = [
        'All' => '🛍️',
        'Phones' => '📱',
        'Computers' => '💻',
        'Audio' => '🎧',
        'Mouse' => '🖱️',
        'Keyboards' => '⌨️',
    ];
    $search = $search ?? '';
@endphp

{{-- Results Count --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">
        Showing <span class="font-semibold text-gray-800">{{ $products->count() }}</span>
        @if ($search !== '')
            results for <span class="font-semibold text-blue-600">"{{ $search }}"</span>
        @elseif ($category !== 'All')
            results in <span class="font-semibold text-blue-600">{{ $category }}</span>
        @else
            products
        @endif
    </p>
</div>

{{-- Product Grid --}}
@if ($products->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center text-4xl mb-4">
            {{ $search !== '' ? '🔍' : '📭' }}
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-1">No Products Found</h3>
        <p class="text-gray-500 text-sm">
            @if ($search !== '')
                No products match <span class="font-semibold">"{{ $search }}"</span>. Try a different keyword.
            @else
                There are no products in this category yet.
            @endif
        </p>
        <button onclick="clearSearch()"
            class="mt-6 px-6 py-2.5 rounded-full bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
            {{ $search !== '' ? 'Clear Search' : 'View All Products' }}
        </button>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 items-start">
        @foreach ($products as $product)
            <div
                class="group relative bg-white rounded-2xl border border-gray-100 hover:border-blue-200 hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col cursor-pointer">

                {{-- Image --}}
                <div
                    class="relative bg-gradient-to-br from-gray-50 to-gray-100 aspect-square flex items-center justify-center p-5 overflow-hidden">

                    @if ($product->stock <= 0)
                        <span
                            class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-gray-400 text-white z-10">Out
                            of Stock</span>
                    @elseif($product->stock <= 5)
                        <span
                            class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-500 text-white z-10">Low
                            Stock</span>
                    @endif

                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                            class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500 mix-blend-multiply"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="hidden w-full h-full items-center justify-center text-5xl">
                            {{ $categoryIcons[$product->category->name ?? ''] ?? '📦' }}
                        </div>
                    @else
                        <div class="flex w-full h-full items-center justify-center text-5xl">
                            {{ $categoryIcons[$product->category->name ?? ''] ?? '📦' }}
                        </div>
                    @endif

                    {{-- Add to Cart --}}
                    @auth
                        <button onclick="addToCart({{ $product->id }})"
                            class="absolute bottom-3 right-3 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 hover:bg-blue-700"
                            aria-label="Add to cart">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-cart" viewBox="0 0 16 16">
                                <path
                                    d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                            </svg>
                        </button>
                    @else
                        <button onclick="showGuestCartModal()"
                            class="absolute bottom-3 right-3 w-8 h-8 rounded-full bg-gray-800 text-white flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 hover:bg-gray-700"
                            aria-label="Login required to add to cart">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-cart" viewBox="0 0 16 16">
                                <path
                                    d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                            </svg>
                        </button>
                    @endauth
                </div>

                {{-- Info --}}
                <div class="p-4 flex flex-col gap-1.5">
                    <span
                        class="text-[10px] font-semibold text-blue-500 uppercase tracking-wider">{{ $product->category->name ?? 'Uncategorized' }}</span>
                    <h3
                        class="text-sm font-semibold text-gray-800 leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors">
                        {{ $product->name }}
                    </h3>
                    {{-- Expandable Description (accordion + gradient fade) --}}
                    <div x-data="{ open: false, uid: {{ $product->id }} }" @close-descs.window="if ($event.detail.except !== uid) open = false"
                        class="mt-0.5">
                        <div class="relative">
                            <div class="overflow-hidden transition-all duration-300 ease-in-out text-[11px] text-gray-400 leading-[1.5rem]"
                                :style="open ? 'max-height: 20rem' : 'max-height: 4.5rem'">{{ $product->description }}
                            </div>
                            {{-- Soft gradient fade when collapsed --}}
                            <div x-show="!open"
                                class="absolute bottom-0 left-0 right-0 h-6 bg-gradient-to-t from-white to-transparent pointer-events-none">
                            </div>
                        </div>
                        <button @click.stop="open = !open; if (open) $dispatch('close-descs', { except: uid })"
                            class="text-[10px] text-gray-500 hover:text-blue-700 font-semibold mt-1 focus:outline-none transition-colors duration-150"
                            x-text="open ? 'See Less' : 'See More'"></button>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-50 flex items-center justify-between">
                        <p class="text-base font-notosan text-gray-900">${{ number_format($product->price, 2) }}</p>
                        <span class="text-[10px] text-gray-400 font-medium">Stock:
                            {{ $product->stock ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
