<x-admin-layout>
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
                                @if ($item->product && $item->product->product_image)
                                    <img src="{{ asset($item->product->product_image) }}"
                                        class="w-full h-full object-contain mix-blend-multiply">
                                @else
                                    <span class="text-2xl">📦</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-800">
                                    {{ $item->product->name ?? 'Deleted Product' }}
                                </p>
                                <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} x
                                    ${{ number_format($item->price, 2) }}</p>
                            </div>
                            <div class="font-bold text-gray-900">
                                ${{ number_format($item->quantity * $item->price, 2) }}
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div
                    class="px-6 py-4 bg-slate-50 border-t border-gray-100 flex justify-between items-center font-bold text-gray-900">
                    <span>Total Amount</span>
                    <span class="text-lg">${{ number_format($order->total_price, 2) }}</span>
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
