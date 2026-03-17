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
                        class="px-6 py-2 text-xs font-bold text-gray-600  tracking-wider border-y border-gray-100">
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
                                    class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full text-[10px] font-bold">Pending</span>
                            @elseif(strtolower($order->status) === 'paid')
                                <span
                                    class="text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full text-[10px] font-bold">Paid</span>
                            @elseif(strtolower($order->status) === 'processing')
                                <span
                                    class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full text-[10px] font-bold">Processing</span>
                            @elseif(strtolower($order->status) === 'completed')
                                <span
                                    class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full text-[10px] font-bold">Completed</span>
                            @else
                                <span
                                    class="text-red-600 bg-red-50 px-2.5 py-1 rounded-full text-[10px] font-bold">Cancelled</span>
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
                    <td colspan="6" class="px-6 py-24 text-center bg-gray-50/30">
                        <div class="flex flex-col items-center justify-center max-w-[320px] mx-auto text-center">
                            <div class="w-20 h-20 bg-white shadow-sm border border-gray-100 rounded-[24px] flex items-center justify-center mb-6">
                                <svg class="w-10 h-10 text-gray-300 transition-transform group-hover:scale-110 duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">
                                @if(request('date'))
                                    There is no order on {{ \Carbon\Carbon::parse(request('date'))->format('d/M/y') }}
                                @else
                                    No Orders Found
                                @endif
                            </h3>
                            <p class="text-gray-500 text-sm leading-relaxed">
                                @if(request('date'))
                                    We couldn't find any transactions for the selected date. Try choosing another period or clear the filter.
                                @else
                                    Your order list is currently empty. Once transactions start coming in, they will appear right here.
                                @endif
                            </p>
                        </div>
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
