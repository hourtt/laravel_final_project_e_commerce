<x-admin-layout>

    @php
        $categoryColors = [
            'Phones' => [
                'bg' => 'bg-rose-50',
                'text' => 'text-rose-600',
                'dot' => 'bg-rose-500',
            ],
            'Computers' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'dot' => 'bg-blue-500'],
            'Audio' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'dot' => 'bg-violet-500'],
            'Mouse' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'dot' => 'bg-green-500'],
            'Keyboards' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'dot' => 'bg-amber-500'],
        ];
    @endphp


    <!-- ======================== STATS GRID ======================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        <!-- Total Products -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">All
                    Products</span>
            </div>
            <p class="text-3xl font-stat text-gray-900">{{ $totalProducts }}</p>
            <p class="text-gray-500 text-sm mt-1">Total Products Listed</p>
        </div>

        <!-- Total Stock -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                        <path fill-rule="evenodd"
                            d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">In
                    Stock</span>
            </div>
            <p class="text-3xl font-stat text-gray-900">{{ number_format($totalStock) }}</p>
            <p class="text-gray-500 text-sm mt-1">Total Items in Stock</p>
        </div>

        <!-- Avg Price -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <span
                    class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Average</span>
            </div>
            <p class="text-3xl font-stat text-gray-900">${{ number_format($avgPrice, 2) }}</p>
            <p class="text-gray-00 text-sm mt-1">Average Product Price</p>
        </div>

        <!-- Categories count -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-violet-50 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-violet-600" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                    </svg>
                </div>
                <span
                    class="text-[10px] font-semibold text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full">Active</span>
            </div>
            <p class="text-3xl font-stat text-gray-900">{{ $categories }}</p>
            <p class="text-gray-500 text-sm mt-1">Product Categories</p>
        </div>
    </div>

    <!-- ====================== MAIN CONTENT ROW ====================== -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- ============ PRODUCT TABLE (LEFT, 2/3) ============ -->
        <div id="recent-products"
            class="scroll-mt-6 xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Product Stocks</h2>
                    <p class="text-xs text-gray-400 mt-0.5">All products are listed with their available stock as
                        descending order</p>
                </div>
                <a href="{{ route('admin.products.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-colors shadow-sm shadow-blue-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Add Product
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 tracking-wider px-6 py-3">
                                Product</th>
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
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentProducts as $product)
                            @php 
                                $clr = $categoryColors[$product->category->name ?? ''] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400'];
                                $catIcon = $product->category->icon ?? '📦';
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @if ($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                class="w-10 h-10 object-contain rounded-lg flex-shrink-0 bg-gray-50 p-1 border border-gray-100">
                                        @else
                                            <div
                                                class="w-10 h-10 {{ $clr['bg'] }} rounded-lg flex items-center justify-center text-lg flex-shrink-0">
                                                @if(str_contains($catIcon, 'images/category/'))
                                                    <img src="{{ asset(str_replace('public/', '', $catIcon)) }}" 
                                                         alt="{{ $product->category->name ?? '' }}" 
                                                         class="w-6 h-6 object-contain">
                                                @else
                                                    {{ $catIcon }}
                                                @endif
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm leading-tight">
                                                {{ $product->name }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span
                                        class="inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $clr['bg'] }} {{ $clr['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $clr['dot'] }}"></span>
                                        {{ $product->category->name ?? 'None' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <p class="text-gray-900">${{ number_format($product->price, 2) }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if ($product->stock <= 0)
                                        <span
                                            class="inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Out
                                        </span>
                                    @elseif($product->stock <= 5)
                                        <span
                                            class="inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Low
                                            ({{ $product->stock }})
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ $product->stock }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                            class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors"
                                            title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete {{ addslashes($product->name) }}?');">
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
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                    No products found. <a href="{{ route('admin.products.create') }}"
                                        class="text-blue-600 font-semibold hover:underline">Add
                                        your first product →</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($recentProducts->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-white" x-data="{ show: false }"
                    x-init="setTimeout(() => show = true, 100)">
                    <div class="transition-all duration-500 ease-out transform"
                        :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                        {{ $recentProducts->links() }}
                    </div>
                </div>
            @endif
        </div>

        <!-- ============ CATEGORY BREAKDOWN (RIGHT, 1/3) ============ -->
        <div class="flex flex-col gap-6">

            <!-- Category Stats -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">By Category</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Product distribution</p>
                </div>
                <div class="p-5 space-y-3">
                    @foreach ($categoryStats as $stat)
                        @php 
                            $clr = $categoryColors[$stat->name] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400'];
                            $catIcon = $stat->icon ?? '📦';
                        @endphp
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 {{ $clr['bg'] }} rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                                @if(str_contains($catIcon, 'images/category/'))
                                    <img src="{{ asset(str_replace('public/', '', $catIcon)) }}" 
                                         alt="{{ $stat->name }}" 
                                         class="w-5 h-5 object-contain">
                                @else
                                    {{ $catIcon }}
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold text-gray-700">{{ $stat->name }}</span>
                                    <span class="text-xs font-bold text-gray-900">{{ $stat->products_count }}
                                        items</span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $clr['dot'] }} rounded-full transition-all duration-700"
                                        style="width: {{ $totalProducts > 0 ? round(($stat->products_count / $totalProducts) * 100) : 0 }}%">
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-0.5">Stock:
                                    {{ number_format($stat->products_sum_stock ?? 0) }}
                                    units</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</x-admin-layout>
