<x-user-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Breadcrumbs --}}
        <nav class="flex mb-8 text-sm font-medium" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-blue-600 transition-colors">Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('home', ['category' => $product->category->name ?? 'All']) }}"
                            class="text-gray-500 hover:text-blue-600 transition-colors">{{ $product->category->name ?? 'Category' }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-800 font-semibold">{{ $product->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col lg:flex-row gap-12 items-start">
            {{-- Left Side: Image Container --}}
            <div class="w-full lg:w-1/2">
                <div
                    class="aspect-square rounded-[24px] bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-100 flex items-center justify-center p-12 overflow-hidden shadow-sm relative group">
                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                            class="w-full h-full object-contain mix-blend-multiply transition-transform duration-700 group-hover:scale-105">
                    @else
                        <div class="text-8xl opacity-20">📦</div>
                    @endif

                    {{-- Visual decorative element --}}
                    <div
                        class="absolute top-6 right-6 px-4 py-2 rounded-full bg-white/80 backdrop-blur-md border border-white shadow-sm text-xs font-bold text-gray-500 uppercase tracking-widest">
                        Model 2026
                    </div>
                </div>
            </div>

            {{-- Right Side: Product Info --}}
            <div class="w-full lg:w-1/2 flex flex-col pt-4">
                <span class="text-sm font-bold text-blue-600 uppercase tracking-[0.2em] mb-3">
                    {{ $product->category->name ?? 'Uncategorized' }}
                </span>

                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-4">
                    {{ $product->name }}
                </h1>

                <div class="flex items-center gap-4 mb-8">
                    <span class="text-3xl font-extrabold text-gray-900">
                        ${{ number_format($product->price, 2) }}
                    </span>
                    <span
                        class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-wider border border-emerald-100">
                        In Stock
                    </span>
                </div>

                <div class="border-t border-gray-100 pt-8 mb-8">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Description</h3>
                    <p class="text-gray-600 leading-relaxed text-lg">
                        {{ $product->description }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 mt-auto pt-8 border-t border-gray-100"
                    x-data="{ qty: 1, stock: {{ $product->stock }} }">
                    {{-- Quantity Selector --}}
                    <div
                        class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-2xl p-1 h-14 w-36">
                        <button @click="if(qty > 1) qty--"
                            class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white hover:shadow-sm text-gray-500 transition-all duration-200 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4" />
                            </svg>
                        </button>
                        <input type="number" x-model="qty" readonly
                            class="w-12 bg-transparent border-none text-center font-bold text-gray-900 focus:ring-0 px-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <button @click="if(qty < stock) qty++"
                            class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white hover:shadow-sm text-gray-500 transition-all duration-200 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>

                    {{-- Add to Cart Button --}}
                    @auth
                        <button
                            onclick="addToCart({{ $product->id }}, document.querySelector('input[type=number]').value)"
                            class="flex-1 w-full bg-gray-900 hover:bg-black text-white font-bold py-4 px-8 rounded-2xl shadow-xl shadow-gray-900/20 active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                            </svg>
                            Add to Cart
                        </button>
                    @else
                        <button onclick="showGuestCartModal()"
                            class="flex-1 w-full bg-gray-900 hover:bg-black text-white font-bold py-4 px-8 rounded-2xl shadow-xl shadow-gray-900/10 transition-all duration-300 flex items-center justify-center gap-3">
                            Login to Purchase
                        </button>
                    @endauth
                </div>

                <div class="mt-8 flex items-center gap-6">
                    <div class="flex items-center gap-2 text-xs text-gray-400 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.952 11.952 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        2 Year Warranty
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-400 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Express Delivery
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>
