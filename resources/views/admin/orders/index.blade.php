<x-admin-layout>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-gray-900">Orders</h2>
                <p class="text-xs text-gray-400 mt-0.5">Manage customer orders</p>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ request('date') }}"
                    class="text-sm border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-2 px-3">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                    Filter
                </button>
                @if (request('date'))
                    <a href="{{ route('admin.orders.index') }}"
                        class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-200 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm table-fixed min-w-[800px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100">
                        <th class="w-[15%] text-center text-xs font-semibold text-gray-500 tracking-wider px-6 py-3">
                            Order ID</th>
                        <th class="w-[15%] text-center text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Customer</th>
                        <th class="w-[15%] text-center text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Total ($)</th>
                        <th class="w-[15%] text-center text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Status</th>
                        <th class="w-[15%] text-center text-xs font-semibold text-gray-500 tracking-wider px-4 py-3">
                            Date</th>
                        <th class="w-[15%] text-right text-xs font-semibold text-gray-500 tracking-wider px-6 py-3">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $dateStr => $ordersGroup)
                        <!-- Date Group Header -->
                        <tr class="bg-gray-100/60 transition-colors">
                            <td colspan="6"
                                class="px-6 py-2 text-xs font-bold text-gray-600 uppercase tracking-wider border-y border-gray-100">
                                {{ $dateStr }}
                            </td>
                        </tr>

                        <!-- Orders for this Date -->
                        @foreach ($ordersGroup as $order)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5 text-center font-bold text-gray-800">
                                    #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3.5 text-center text-gray-600 truncate">
                                    {{ $order->user->name ?? 'Unknown' }}</td>
                                <td class="px-4 py-3.5 text-center font-bold text-gray-900">
                                    ${{ number_format($order->total_price, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if (strtolower($order->status) === 'pending')
                                        <span
                                            class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">Pending</span>
                                    @elseif(strtolower($order->status) === 'paid')
                                        <span
                                            class="text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">Paid</span>
                                    @elseif(strtolower($order->status) === 'processing')
                                        <span
                                            class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">Processing</span>
                                    @elseif(strtolower($order->status) === 'completed')
                                        <span
                                            class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">Completed</span>
                                    @else
                                        <span
                                            class="text-red-600 bg-red-50 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">Cancelled</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center text-gray-500 text-xs">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-bold transition-colors">
                                            View
                                        </a>

                                        @if (in_array(strtoupper($order->status), ['PENDING', 'CANCELLED', 'FAILED']))
                                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-xs font-bold transition-colors">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
