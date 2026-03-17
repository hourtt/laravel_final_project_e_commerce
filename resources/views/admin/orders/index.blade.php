<x-admin-layout>
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm">
        <div class="px-6 py-6 border-b border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Orders Management</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Track and manage customer purchase history</p>
                </div>

                {{-- Redesigned Date Filter --}}
                <x-date-filter />
            </div>
        </div>

        <div id="orders-table-container" class="transition-opacity duration-300">
            @include('admin.orders.table')
        </div>
    </div>
</x-admin-layout>
