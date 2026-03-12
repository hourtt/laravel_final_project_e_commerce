<x-user-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Page Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>
                <p class="text-sm text-gray-400 mt-1">View your complete purchase history</p>
            </div>
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors group">
                <span
                    class="w-8 h-8 rounded-full bg-gray-100 group-hover:bg-blue-50 flex items-center justify-center transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none"
                        viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </span>
                Shop
            </a>
        </div>

        {{-- Orders List --}}
        @if ($orders->isEmpty())
            <div
                class="flex flex-col items-center justify-center py-24 text-center bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-4xl mb-5">📦</div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">No orders yet</h3>
                <p class="text-gray-400 text-sm mb-6">You haven't placed any orders. Start shopping!</p>
                <a href="{{ route('home') }}"
                    class="px-6 py-2.5 rounded-full bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-colors shadow-md shadow-blue-600/20">
                    Browse Products
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($orders as $order)
                    @php
                        $statusMap = [
                            'pending' => ['label' => 'Pending', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
                            'paid' => ['label' => 'Paid', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
                            'processing' => ['label' => 'Processing', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
                            'completed' => [
                                'label' => 'Completed',
                                'bg' => 'bg-emerald-50',
                                'text' => 'text-emerald-600',
                            ],
                        ];
                        $s = $statusMap[strtolower($order->status)] ?? [
                            'label' => ucfirst($order->status),
                            'bg' => 'bg-red-50',
                            'text' => 'text-red-600',
                        ];
                    @endphp
                    <a href="{{ route('user.orders.show', $order->id) }}"
                        class="group flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-2xl border border-gray-100 hover:border-blue-200 hover:shadow-lg transition-all duration-200 p-5">

                        {{-- Left: ID + Date + Items --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-xl shrink-0 mt-0.5">
                                🛍️
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                    Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $order->created_at->format('M d, Y · h:i A') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1.5">
                                    {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}:
                                    <span class="text-gray-700 font-medium">
                                        {{ $order->items->pluck('product.name')->filter()->take(2)->implode(', ') }}{{ $order->items->count() > 2 ? ' & more' : '' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        {{-- Right: Status + Total + Arrow --}}
                        @php
                            $itemsSubtotal = $order->items->sum(fn($i) => $i->quantity * $i->price);
                            $hasDiscount = $order->total_price < $itemsSubtotal - 0.001; // float tolerance
                        @endphp
                        <div class="flex items-center gap-4 sm:shrink-0">
                            <span
                                class="px-3 py-1 rounded-full text-[11px] font-bold uppercase {{ $s['bg'] }} {{ $s['text'] }}">
                                {{ $s['label'] }}
                            </span>
                            <div class="text-right">
                                <p class="text-base font-bold text-gray-900 min-w-[80px]">
                                    ${{ number_format($order->total_price, 2) }}
                                </p>
                                @if ($hasDiscount)
                                    <p
                                        class="text-[10px] font-semibold text-emerald-600 flex items-center justify-end gap-0.5 mt-0.5">
                                        🏷️ Voucher applied
                                    </p>
                                @endif
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition-all"
                                fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($orders->hasPages())
                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @endif
        @endif
    </div>
</x-user-layout>
