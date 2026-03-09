<x-admin-layout>

    {{-- Back navigation --}}
    <div class="mb-5">
        <a href="{{ route('admin.orders.index') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors group">
            <span
                class="w-8 h-8 rounded-full bg-gray-100 group-hover:bg-blue-50 flex items-center justify-center transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none"
                    viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </span>
            Orders
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-base font-bold text-gray-900">Order Items</h2>
                    <span class="text-xs font-bold text-gray-500">Order
                        #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>

                <ul class="divide-y divide-gray-50">
                    @foreach ($order->items as $item)
                        <li class="p-6 flex items-center gap-4">
                            <div class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center p-2">
                                @if ($item->product && $item->product->image_url)
                                    <img src="{{ $item->product->image_url }}"
                                        class="w-full h-full object-contain mix-blend-multiply"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span class="text-2xl hidden items-center justify-center w-full h-full">📦</span>
                                @else
                                    <span class="text-2xl flex items-center justify-center w-full h-full">📦</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-800">
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
                                <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} x
                                    ${{ number_format($item->price, 2) }}</p>
                            </div>
                            <div class="font-bold text-gray-900">
                                ${{ number_format($item->quantity * $item->price, 2) }}
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-gray-100">
                    @php
                        $itemsSubtotal = $order->items->sum(fn($i) => $i->quantity * $i->price);

                        // Use the stored voucher_discount if available (orders created after migration).
                        // Fall back to calculating the difference for older orders where the column is null.
                        $voucherCode = $order->voucher_code;
                        $voucherDiscount =
                            $order->voucher_discount ??
                            ($itemsSubtotal - $order->total_price > 0.01 ? $itemsSubtotal - $order->total_price : null);
                        $hasDiscount = $voucherDiscount !== null && $voucherDiscount > 0;
                    @endphp

                    {{-- Subtotal + voucher rows — only shown when a discount was applied --}}
                    @if ($hasDiscount)
                        <div class="flex justify-between items-center px-6 py-3 text-sm text-gray-500">
                            <span>Subtotal</span>
                            <span class="font-semibold">${{ number_format($itemsSubtotal, 2) }}</span>
                        </div>

                        {{-- Voucher discount row --}}
                        <div
                            class="flex justify-between items-center px-6 py-3 bg-emerald-50 border-y border-emerald-100">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-emerald-700">🏷️ Voucher Discount</span>
                                @if ($voucherCode)
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-bold uppercase tracking-wide font-mono">
                                        {{ $voucherCode }}
                                    </span>
                                @endif
                            </div>
                            <span
                                class="text-sm font-bold text-emerald-700">-${{ number_format($voucherDiscount, 2) }}</span>
                        </div>
                    @endif

                    {{-- Final total — always from orders.total_price --}}
                    <div class="px-6 py-4 bg-slate-50 flex justify-between items-center font-bold text-gray-900">
                        <span>Total Amount</span>
                        <span class="text-lg">${{ number_format($order->total_price, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Customer Details</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                        {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ $order->user->name ?? 'Unknown Customer' }}</p>
                        <p class="text-xs text-gray-500">{{ $order->user->email ?? 'No email' }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-400">Order Date</p>
                <p class="text-sm font-semibold text-gray-800">{{ $order->created_at->format('M d, Y h:i A') }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Update Status</h3>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <select name="status"
                            class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm font-semibold"
                            required>
                            <option value="Pending" {{ strtolower($order->status) === 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="Paid" {{ strtolower($order->status) === 'paid' ? 'selected' : '' }}>Paid
                            </option>
                            <option value="Processing"
                                {{ strtolower($order->status) === 'processing' ? 'selected' : '' }}>
                                Processing
                            </option>
                            <option value="Completed"
                                {{ strtolower($order->status) === 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="Cancelled"
                                {{ strtolower($order->status) === 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                    </div>
                    <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition">Update
                        Order</button>
                </form>
            </div>

            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this order?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full py-2 border border-red-200 text-red-600 bg-red-50 hover:bg-red-100 rounded-xl font-bold text-sm transition text-center">Delete
                    Order</button>
            </form>
        </div>
    </div>
</x-admin-layout>
