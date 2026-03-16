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
    <div class="flex flex-col items-center justify-center py-24 text-center animate-fade-up">
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
        <button @click="activeSearch = ''; search('')"
            class="mt-6 px-6 py-2.5 rounded-full bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-all active:scale-95 shadow-md">
            {{ $search !== '' ? 'Clear Search' : 'View All Products' }}
        </button>
    </div>
@else
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 items-start">
        @foreach ($products as $index => $product)
            <div onclick="window.location.href='{{ route('products.show', $product->id) }}'"
                class="group relative bg-white rounded-[24px] border border-gray-100/80 hover:border-blue-200 hover:shadow-[0_20px_50px_rgba(59,130,246,0.12)] hover:-translate-y-1.5 transition-all duration-500 overflow-hidden flex flex-col cursor-pointer animate-fade-up"
                style="animation-delay: {{ $index * 50 }}ms; opacity: 0;">

                {{-- Image --}}
                <div
                    class="relative bg-gradient-to-br from-gray-50 to-gray-100 aspect-square flex items-center justify-center p-6 overflow-hidden">

                    @if ($product->stock <= 0)
                        <span
                            class="absolute top-3 left-3 px-2 py-0.5 rounded-full text-[10px] font-bold  bg-gray-400 text-white z-10">Out
                            of Stock</span>
                    @elseif($product->stock <= 5)
                        <span
                            class="absolute top-3 left-3 px-2 py-0.5 rounded-full text-[10px] font-bold  bg-amber-500 text-white z-10">Low
                            Stock</span>
                    @endif

                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                            class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-700 mix-blend-multiply"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="hidden w-full h-full items-center justify-center text-5xl">
                            {{ $categoryIcons[$product->category->name ?? ''] ?? '📦' }}
                        </div>
                    @else
                        <div class="flex w-full h-full items-center justify-center text-5xl">
                            {{ $categoryIcons[$product->category->name ?? ''] ?? '📦' }}
                        </div>
                    @endif

                    @auth
                        <button onclick="event.stopPropagation(); if(typeof addToCart === 'function') addToCart({{ $product->id }})"
                            class="absolute bottom-3 right-3 w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 hover:bg-blue-600 hover:scale-110 active:scale-90"
                            aria-label="Add to cart">
                            <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor">
                                <circle cx="16.5" cy="18.5" r="1.5" />
                                <circle cx="9.5" cy="18.5" r="1.5" />
                                <path
                                    d="M18 16H8a1 1 0 0 1-.958-.713L4.256 6H3a1 1 0 0 1 0-2h2a1 1 0 0 1 .958.713L6.344 6H21a1 1 0 0 1 .937 1.352l-3 8A1 1 0 0 1 18 16zm-9.256-2h8.563l2.25-6H6.944z" />
                            </svg>
                        </button>
                    @endauth
                </div>

                {{-- Info --}}
                <div class="p-4 flex flex-col gap-1">
                    <span
                        class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">{{ $product->category->name ?? 'Uncategorized' }}</span>
                    <h3
                        class="text-sm font-semibold text-gray-800 leading-tight line-clamp-1 group-hover:text-blue-600 transition-colors">
                        {{ $product->name }}
                    </h3>

                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-[15px] font-bold text-gray-900">${{ number_format($product->price, 2) }}</p>
                        <span class="text-[10px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded-md">Stock: {{ $product->stock ?? '0' }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-12 flex justify-center">
        {{ $products->links() }}
    </div>
@endif
