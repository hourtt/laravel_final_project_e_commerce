<x-admin-layout>

    <div class="mb-6">
        <a href="{{ route('admin.vouchers.index') }}"
            class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-800 transition-colors font-semibold mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Vouchers
        </a>
        <h1 class="text-xl font-bold text-gray-900">Edit Voucher — <span
                class="font-mono text-indigo-600">{{ $voucher->code }}</span></h1>
        <p class="text-xs text-gray-400 mt-0.5">Used {{ $voucher->used_count }} time(s) so far</p>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            @include('admin.vouchers._form')
        </div>
    </div>

</x-admin-layout>
