<x-user-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Back + Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <a href="{{ route('user.orders') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors mb-2 group">
                    <span
                        class="w-8 h-8 rounded-full bg-gray-100 group-hover:bg-blue-50 flex items-center justify-center transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none"
                            viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </span>
                    My Orders
                </a>
                <h1 class="text-2xl font-bold text-gray-900">
                    Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
                <p class="text-sm text-gray-400 mt-0.5">
                    Purchased on {{ $order->created_at->format('F d, Y \a\t h:i A') }}
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
                                        {{ $item->product_name ?? ($item->product->name ?? 'Deleted Product') }}
                                    </p>
                                    @if ($item->voucher_code)
                                        <div class="mt-1 flex items-center gap-1.5">
                                            <span
                                                class="px-2 py-0.5 rounded-md bg-emerald-100/50 text-emerald-600 border border-emerald-200 text-[10px] font-bold uppercase tracking-wide font-mono">
                                                🏷️ {{ $item->voucher_code }}
                                            </span>
                                            @if ($item->voucher_discount > 0)
                                                <span class="text-[11px] font-bold text-emerald-600">
                                                    (-${{ number_format($item->voucher_discount, 2) }})
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}
                                    </p>
                                </div>

                                {{-- Line total --}}
                                <p class="text-sm font-bold text-gray-900 shrink-0">
                                    ${{ number_format($item->quantity * $item->price, 2) }}
                                </p>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Total footer: shows subtotal, discount row (if any), and final order total --}}
                    @php
                        $itemsSubtotal = $order->items->sum(fn($i) => $i->quantity * $i->price);
                        $discountAmount = $itemsSubtotal - $order->total_price;
                        $hasDiscount = $discountAmount > 0.001; // float tolerance
                    @endphp
                    <div class="border-t border-gray-100">
                        {{-- Subtotal row (only shown when a discount exists) --}}
                        @if ($hasDiscount)
                            <div class="flex justify-between items-center px-5 py-3 text-sm text-gray-500">
                                <span>Subtotal</span>
                                <span class="font-semibold">${{ number_format($itemsSubtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center px-5 py-3 text-sm text-emerald-600">
                                <span class="flex items-center gap-1.5 font-semibold">
                                    🏷️ Voucher Discount
                                </span>
                                <span class="font-bold">-${{ number_format($discountAmount, 2) }}</span>
                            </div>
                        @endif
                        {{-- Final total — always from orders.total_price --}}
                        <div class="flex justify-between items-center px-5 py-4 bg-slate-50 border-t border-gray-100">
                            <span class="text-sm font-bold text-gray-700">Order Total</span>
                            <span
                                class="text-xl font-bold text-gray-900">${{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Shipping Address Card --}}
                <div class="bg-gray-50 rounded-2xl border border-gray-100 p-6 mt-6 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Shipping Address
                    </h3>
                    
                    @if($order->shipping_full_name || $order->address_id)
                        @php
                            $name = $order->shipping_full_name ?: ($order->shippingAddress->full_name ?? '');
                            $phone = $order->shipping_phone_number ?: ($order->shippingAddress->phone_number ?? '');
                            $street = $order->shipping_street_address ?: ($order->shippingAddress->street_address ?? '');
                            $city = $order->shipping_city ?: ($order->shippingAddress->city ?? '');
                            $state = $order->shipping_state ?: ($order->shippingAddress->state ?? '');
                            $postcode = $order->shipping_postal_code ?: ($order->shippingAddress->postal_code ?? '');
                            $country = $order->shipping_country ?: ($order->shippingAddress->country ?? '');
                        @endphp
                        
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-gray-800">{{ $name }}</p>
                            <p class="text-xs text-gray-500 font-medium">{{ $phone }}</p>
                            <div class="text-xs text-gray-600 mt-2 space-y-0.5 leading-relaxed">
                                <p>{{ $street }}</p>
                                <p>{{ $city }}, {{ $state }} {{ $postcode }}</p>
                                <p class="uppercase tracking-wide font-bold text-[10px] text-gray-400 pt-1">{{ $country }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">No address on record</p>
                    @endif
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
                        <div class="pt-3 border-t border-gray-100 space-y-2">
                            @if ($hasDiscount)
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Subtotal</span>
                                    <span class="font-semibold">${{ number_format($itemsSubtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm text-emerald-600">
                                    <span class="font-semibold">🏷️ Voucher</span>
                                    <span class="font-bold">-${{ number_format($discountAmount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between pt-2 border-t border-gray-100">
                                <span class="font-bold text-gray-900">Total</span>
                                <span
                                    class="font-bold text-gray-900 text-base">${{ number_format($order->total_price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Back to orders --}}
                <a href="{{ route('user.orders') }}"
                    class="inline-flex items-center justify-center gap-2 w-full py-3 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-colors group">
                    <span
                        class="w-6 h-6 rounded-full bg-gray-100 group-hover:bg-blue-100 flex items-center justify-center transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-3.5 h-3.5 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none"
                            viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </span>
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
