<x-admin-layout>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">Orders</h2>
            <p class="text-xs text-gray-400 mt-0.5">Manage customer orders</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">
                            Order ID</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">
                            Customer</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">
                            Total ($)</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">
                            Status</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-3">
                            Date</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5 font-bold text-gray-800">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 truncate max-w-[150px]">
                                {{ $order->user->name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3.5 font-bold text-gray-900">${{ number_format($order->total_price, 2) }}
                            </td>
                            <td class="px-4 py-3.5">
                                @if($order->status === 'Pending')
                                    <span
                                        class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">Pending</span>
                                @elseif($order->status === 'Processing')
                                    <span
                                        class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">Processing</span>
                                @elseif($order->status === 'Completed')
                                    <span
                                        class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">Completed</span>
                                @else
                                    <span
                                        class="text-red-600 bg-red-50 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">Cancelled</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-gray-500 text-xs">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-3.5 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-bold transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
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
    </div>
</x-admin-layout>