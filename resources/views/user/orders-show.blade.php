<x-user-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Back + Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <a href="{{ route('user.orders') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-400 hover:text-blue-600 transition-colors mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    My Orders
                </a>
                <h1 class="text-2xl font-extrabold text-gray-900">
                    Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
                <p class="text-sm text-gray-400 mt-0.5">
                    Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}
                </p>
            </div>

            {{-- Status Badge --}}
            @php
                $statusMap = [
                    'pending' => [
                        'label' => 'Pending',
                        'bg' => 'bg-amber-50',
                        'text' => 'text-amber-600',
                        'ring' => 'ring-amber-200',
                    ],
                    'paid' => [
                        'label' => 'Paid',
                        'bg' => 'bg-indigo-50',
                        'text' => 'text-indigo-600',
                        'ring' => 'ring-indigo-200',
                    ],
                    'processing' => [
                        'label' => 'Processing',
                        'bg' => 'bg-blue-50',
                        'text' => 'text-blue-600',
                        'ring' => 'ring-blue-200',
                    ],
                    'completed' => [
                        'label' => 'Completed',
                        'bg' => 'bg-emerald-50',
                        'text' => 'text-emerald-600',
                        'ring' => 'ring-emerald-200',
                    ],
                ];
                $s = $statusMap[strtolower($order->status)] ?? [
                    'label' => ucfirst($order->status),
                    'bg' => 'bg-red-50',
                    'text' => 'text-red-600',
                    'ring' => 'ring-red-200',
                ];
            @endphp
            <span
                class="self-start sm:self-auto px-4 py-1.5 rounded-full text-sm font-bold uppercase ring-1 {{ $s['bg'] }} {{ $s['text'] }} {{ $s['ring'] }}">
                {{ $s['label'] }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ============ ORDER ITEMS ============ --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50">
                        <h2 class="text-sm font-bold text-gray-900">
                            Order Items
                            <span
                                class="ml-1.5 text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                                {{ $order->items->count() }}
                            </span>
                        </h2>
                    </div>

                    <ul class="divide-y divide-gray-50">
                        @foreach ($order->items as $item)
                            <li class="flex items-center gap-4 p-5">
                                {{-- Product image --}}
                                <div
                                    class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center p-2 shrink-0">
                                    @if ($item->product && $item->product->image_url)
                                        <img src="{{ $item->product->image_url }}"
                                            class="w-full h-full object-contain mix-blend-multiply"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span
                                            class="text-2xl hidden items-center justify-center w-full h-full">📦</span>
                                    @else
                                        <span class="text-2xl flex items-center justify-center w-full h-full">📦</span>
                                    @endif
                                </div>

                                {{-- Name + qty + unit price --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-800 truncate">
                                        {{ $item->product->name ?? 'Deleted Product' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}
                                    </p>
                                </div>

                                {{-- Line total --}}
                                <p class="text-sm font-extrabold text-gray-900 shrink-0">
                                    ${{ number_format($item->quantity * $item->price, 2) }}
                                </p>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Total row — computed from actual items, not stored total_price --}}
                    @php $computedTotal = $order->items->sum(fn($i) => $i->quantity * $i->price); @endphp
                    <div class="flex justify-between items-center px-5 py-4 bg-slate-50 border-t border-gray-100">
                        <span class="text-sm font-bold text-gray-700">Order Total</span>
                        <span
                            class="text-xl font-extrabold text-gray-900">${{ number_format($computedTotal, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- ============ SUMMARY SIDEBAR ============ --}}
            <div class="space-y-4">

                {{-- Order Summary card --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                    <h3 class="text-sm font-bold text-gray-900">Order Summary</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-500">
                            <span>Order ID</span>
                            <span
                                class="font-bold text-gray-800">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Date</span>
                            <span class="font-semibold text-gray-700">{{ $order->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Items</span>
                            <span class="font-semibold text-gray-700">{{ $order->items->count() }}</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Status</span>
                            <span class="font-bold {{ $s['text'] }}">{{ $s['label'] }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-100 flex justify-between">
                            <span class="font-bold text-gray-900">Total</span>
                            <span
                                class="font-extrabold text-gray-900 text-base">${{ number_format($computedTotal, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Back to orders --}}
                <a href="{{ route('checkout') }}"
                    class="flex items-center justify-center gap-2 w-full py-3 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to My Orders
                </a>

                {{-- Continue shopping --}}
                <a href="{{ route('home') }}"
                    class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition-colors shadow-md shadow-blue-600/20">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</x-user-layout>
