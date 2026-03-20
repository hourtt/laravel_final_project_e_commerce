@php
    $search = $search ?? '';
@endphp

{{-- Results Count & Filters Row --}}
<div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-6 mb-10">
    {{-- Left Side: Showing Count --}}
    <div class="flex items-center gap-4 bg-white p-4 rounded-3xl border border-gray-100 shadow-sm flex-1">
        <div
            class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-xl shadow-blue-600/20 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                    clip-rule="evenodd" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-bold text-gray-900 leading-tight">
                Showing <span class="text-blue-600 italic">{{ $products->total() }}</span>
                {{ Str::plural('result', $products->total()) }}
            </p>
            <p class="text-xs text-gray-400 mt-1 truncate">
                @if ($search !== '')
                    for <span class="font-bold text-gray-600 italic">"{{ $search }}"</span>
                @elseif ($brandName !== '')
                    for <span class="font-bold text-gray-600 italic">{{ $brandName }}</span> in <span
                        class="font-bold text-gray-600 italic">{{ $category }}</span>
                @elseif ($category !== 'All')
                    in <span class="font-bold text-gray-600 italic">{{ $category }}</span>
                @else
                    across all categories
                @endif
            </p>
        </div>
    </div>

    {{-- Right Side: Premium Brand Select --}}
    @if (count($brands) > 0)
        <div x-data="{
            open: false,
            selected: '{{ $brandName ?: 'All Brands' }}',
            brands: @js($brands),
            toggle() { this.open = !this.open },
            select(brand) {
                this.selected = brand || 'All Brands';
                this.open = false;
                this.activeBrand = brand;
                this.brandFilter(brand);
            }
        }" class="relative w-full lg:w-[280px]">
            <!-- Trigger -->
            <button @click="toggle()" type="button"
                class="relative flex items-center justify-between w-full h-[64px] px-6 bg-white border border-gray-300 rounded-2xl text-left transition-all duration-300 hover:shadow-md focus:outline-none shadow-sm"
                :class="open ? 'ring-2 ring-blue-500 border-transparent shadow-xl' : ''">
                <div class="flex flex-col">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Select
                        Brand</span>
                    <span x-text="selected" class="text-sm font-bold text-gray-800"></span>
                </div>
                <svg class="w-5 h-5 text-gray-300 transition-transform duration-300" :class="open ? 'rotate-180' : ''"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <!-- Dropdown Card -->
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-[-10px] scale-95" @click.away="open = false"
                class="absolute z-50 w-full mt-3 bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden"
                style="display: none;">
                <ul class="max-h-[300px] overflow-y-auto py-3 custom-scrollbar flex flex-col gap-1 px-2">
                    <li @click="select('')" 
                        class="px-4 py-3 cursor-pointer transition-all duration-200 text-sm rounded-xl flex items-center justify-between group"
                        :class="selected === 'All Brands' 
                            ? 'bg-blue-50 text-blue-600 border border-blue-100 font-bold' 
                            : 'text-gray-600 hover:bg-gray-50 border border-transparent'">
                        <span class="flex items-center gap-2">
                            <span class="opacity-60 group-hover:scale-110 transition-transform">🏷️</span>
                            All Brands
                        </span>
                        <template x-if="selected === 'All Brands'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </template>
                    </li>
                    <template x-for="brand in brands" :key="brand">
                        <li @click="select(brand)" 
                            class="px-4 py-3 cursor-pointer transition-all duration-200 text-sm rounded-xl flex items-center justify-between group"
                            :class="selected === brand 
                                ? 'bg-blue-50 text-blue-600 border border-blue-100 font-bold' 
                                : 'text-gray-600 hover:bg-gray-50 border border-transparent'">
                            <span x-text="brand"></span>
                            <template x-if="selected === brand">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </template>
                        </li>
                    </template>
                </ul>
            </div>

            <style>
                .custom-scrollbar::-webkit-scrollbar {
                    width: 4px;
                }

                .custom-scrollbar::-webkit-scrollbar-track {
                    background: transparent;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #E5E7EB;
                    border-radius: 10px;
                }
            </style>
        </div>
    @endif
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
                        <div class="hidden w-full h-full items-center justify-center">
                            @if ($product->category && $product->category->icon && str_contains($product->category->icon, 'images/category/'))
                                <img src="{{ asset(str_replace('public/', '', $product->category->icon)) }}"
                                    alt="{{ $product->category->name }}" class="w-20 h-20 opacity-20 object-contain">
                            @else
                                <span class="text-5xl opacity-20">{{ $product->category->icon ?? '📦' }}</span>
                            @endif
                        </div>
                    @else
                        <div class="flex w-full h-full items-center justify-center">
                            @if ($product->category && $product->category->icon && str_contains($product->category->icon, 'images/category/'))
                                <img src="{{ asset(str_replace('public/', '', $product->category->icon)) }}"
                                    alt="{{ $product->category->name }}" class="w-20 h-20 opacity-20 object-contain">
                            @else
                                <span class="text-5xl opacity-20">{{ $product->category->icon ?? '📦' }}</span>
                            @endif
                        </div>
                    @endif

                    @auth
                        <button
                            onclick="event.stopPropagation(); if(typeof addToCart === 'function') addToCart({{ $product->id }})"
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
                        <span class="text-[10px] text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded-md">Stock:
                            {{ $product->stock ?? '0' }}</span>
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
