{{--
    Shared voucher form fields.
    Variables expected: $voucher (nullable), $products, $randomCode (nullable)
--}}
@php
    $isEdit = isset($voucher) && $voucher->exists;
    $formAction = $isEdit ? route('admin.vouchers.update', $voucher->id) : route('admin.vouchers.store');
    
    // Global Lock Condition: Vouchers used by customers or already expired are read-only.
    $isLocked = $isEdit && ($voucher->used_count > 0 || ($voucher->expires_at && $voucher->expires_at->isPast()));
@endphp

<form action="{{ $formAction }}" method="POST" id="voucher-form">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-6">

        {{-- ── Code ──────────────────────────────────────────────── --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="code">
                Voucher Code <span class="text-red-500">*</span>
            </label>
            
            <div class="relative group">
                @if ($isLocked)
                    {{-- Tooltip/Notice for locked field area --}}
                    <div
                        class="hidden group-hover:block absolute z-10 bottom-full left-0 mb-2 w-72 p-4 bg-red-600 text-white text-[11px] rounded-xl shadow-2xl leading-relaxed">
                        <p class="font-bold border-b border-red-500 pb-1 mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/></svg>
                            Modification Restricted
                        </p>
                        This voucher {{ $voucher->used_count > 0 ? 'has been used by customers' : 'has already expired' }}. 
                        For data integrity, locked vouchers cannot be edited. Please create a new voucher if needed.
                        <div class="absolute top-full left-6 -mt-1 border-4 border-transparent border-t-red-600"></div>
                    </div>
                @endif

                <div class="flex items-center gap-2">
                    <input type="text" id="code" name="code"
                        value="{{ old('code', $voucher->code ?? ($randomCode ?? '')) }}" placeholder="e.g. SUMMER10"
                        maxlength="50"
                        class="flex-1 h-10 px-4 rounded-xl border {{ $errors->has('code') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} text-sm font-mono uppercase tracking-widest focus:border-blue-400 focus:ring-0 outline-none transition-colors 
                               {{ $isLocked ? 'bg-gray-50 text-gray-400 cursor-not-allowed pointer-events-none' : 'bg-white' }}"
                        {{ $isLocked ? 'readonly' : 'required' }}>

                    {{-- Generate random code button --}}
                    <button type="button" id="generate-btn"
                        {{ $isLocked ? 'disabled' : '' }}
                        class="h-10 px-4 rounded-xl text-xs font-semibold transition-colors whitespace-nowrap flex items-center gap-1.5 
                               {{ $isLocked ? 'bg-gray-50 text-gray-300 cursor-not-allowed' : 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Generate
                    </button>
                </div>
            </div>

            @error('code')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- ── Discount Type + Value ───────────────────────────── --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="discount_type">
                    Discount Type <span class="text-red-500">*</span>
                </label>
                <select id="discount_type" name="discount_type"
                    class="w-full h-10 px-4 rounded-xl border {{ $errors->has('discount_type') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} text-sm focus:border-blue-400 focus:ring-0 outline-none transition-colors 
                           {{ $isLocked ? 'bg-gray-50 text-gray-400 cursor-not-allowed pointer-events-none' : 'bg-white' }}"
                    {{ $isLocked ? 'disabled' : 'required' }} onchange="updateValueSuffix()">
                    <option value="percentage"
                        {{ old('discount_type', $voucher->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>
                        Percentage (%)</option>
                    <option value="fixed"
                        {{ old('discount_type', $voucher->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>
                        Fixed Amount ($)</option>
                </select>
                @error('discount_type')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="discount_value">
                    Discount Value <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" id="discount_value" name="discount_value"
                        value="{{ old('discount_value', $voucher->discount_value ?? '') }}" min="0.01"
                        step="0.01" placeholder="0.00"
                        class="w-full h-10 pl-4 pr-10 rounded-xl border {{ $errors->has('discount_value') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} text-sm focus:border-blue-400 focus:ring-0 outline-none transition-colors
                               {{ $isLocked ? 'bg-gray-50 text-gray-400 cursor-not-allowed pointer-events-none' : 'bg-white' }}"
                        {{ $isLocked ? 'readonly' : 'required' }}>
                    <span id="value-suffix"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 pointer-events-none">
                        {{ old('discount_type', $voucher->discount_type ?? 'percentage') === 'percentage' ? '%' : '$' }}
                    </span>
                </div>
                @error('discount_value')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ── Product Scope ───────────────────────────────────── --}}
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="product_id">
                Applies To
            </label>
            <select id="product_id" name="product_id"
                class="w-full h-10 px-4 rounded-xl border {{ $errors->has('product_id') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} text-sm focus:border-blue-400 focus:ring-0 outline-none transition-colors 
                       {{ $isLocked ? 'bg-gray-50 text-gray-400 cursor-not-allowed pointer-events-none' : 'bg-white' }}"
                {{ $isLocked ? 'disabled' : '' }}>
                <option value="">— Any product —</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}"
                        {{ old('product_id', $voucher->product_id ?? '') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} (${{ number_format($product->price, 2) }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-[11px] text-gray-400">Leave blank to allow on any product.</p>
            @error('product_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- ── Usage Limit & Expiry ────────────────────────────── --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="usage_limit">
                    Max Usage Limit
                </label>
                <input type="number" id="usage_limit" name="usage_limit"
                    value="{{ old('usage_limit', $voucher->usage_limit ?? '') }}" min="1"
                    placeholder="Unlimited"
                    class="w-full h-10 px-4 rounded-xl border {{ $errors->has('usage_limit') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} text-sm focus:border-blue-400 focus:ring-0 outline-none transition-colors
                           {{ $isLocked ? 'bg-gray-50 text-gray-400 cursor-not-allowed pointer-events-none' : 'bg-white' }}"
                    {{ $isLocked ? 'readonly' : '' }}>
                <p class="mt-1 text-[11px] text-gray-400">Leave blank for unlimited uses.</p>
                @error('usage_limit')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="expires_at">
                    Expiration Date
                </label>
                <div class="relative group">
                    <input type="datetime-local" id="expires_at" name="expires_at"
                        value="{{ old('expires_at', isset($voucher) && $voucher->expires_at ? $voucher->expires_at->format('Y-m-d\TH:i') : '') }}"
                        class="w-full h-10 px-4 rounded-xl border {{ $errors->has('expires_at') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} text-sm focus:border-blue-400 focus:ring-0 outline-none transition-colors 
                               {{ $isLocked ? 'bg-gray-50 text-gray-400 cursor-not-allowed pointer-events-none' : 'bg-white' }}"
                        {{ $isLocked ? 'readonly' : '' }}>

                    @if ($isLocked)
                        {{-- Tooltip/Notice for locked field --}}
                        <div
                            class="hidden group-hover:block absolute z-10 bottom-full left-0 mb-2 w-64 p-3 bg-red-600 text-white text-[10px] rounded-xl shadow-xl leading-relaxed">
                            <p class="font-bold mb-1">Notice:</p>
                            After this voucher has been used, you cannot change the expiration date anymore.
                            <div class="absolute top-full left-6 -mt-1 border-4 border-transparent border-t-red-600">
                            </div>
                        </div>
                    @endif
                </div>

                @if ($isLocked)
                    <p class="mt-1.5 text-[10px] text-red-500 font-semibold flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Expiry locked (was used by customers)
                    </p>
                @else
                    <p class="mt-1 text-[11px] text-gray-400">Leave blank for no expiry.</p>
                @endif

                @error('expires_at')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ── Status ─────────────────────────────────────────── --}}
        <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 bg-gray-50">
            <div>
                <p class="text-sm font-semibold text-gray-800">Active Status</p>
                <p class="text-xs text-gray-400 mt-0.5">Inactive vouchers cannot be applied at checkout.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer {{ $isLocked ? 'opacity-50 pointer-events-none' : '' }}">
                <input type="checkbox" name="status" value="1" id="status"
                    {{ old('status', $voucher->status ?? true) ? 'checked' : '' }} class="sr-only peer"
                    {{ $isLocked ? 'disabled' : '' }}>
                <div
                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                            peer-checked:after:translate-x-full peer-checked:after:border-white
                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                            after:bg-white after:border-gray-300 after:border after:rounded-full
                            after:h-5 after:w-5 after:transition-all
                            peer-checked:bg-blue-600">
                </div>
            </label>
        </div>

        {{-- ── Social Media Preview ────────────────────────────── --}}
        <div class="p-4 rounded-xl border border-dashed border-indigo-200 bg-indigo-50/50" id="social-preview">
            <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                Social Media Preview
            </p>
            <p class="text-sm font-mono text-indigo-800 leading-relaxed" id="preview-text">
                Use code: <strong id="prev-code">—</strong><br>
                Get <strong id="prev-val">—</strong> OFF
                <span id="prev-product-wrap"> on <strong id="prev-product">Any product</strong></span>
            </p>
        </div>

    </div>{{-- /grid --}}

    {{-- Submit --}}
    @if (!$isLocked)
        <div class="mt-8 flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ $isEdit ? 'Update Voucher' : 'Create Voucher' }}
            </button>
            <a href="{{ route('admin.vouchers.index') }}"
                class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold transition-colors">
                Cancel
            </a>
        </div>
    @else
        <div class="mt-8 p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m11 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Voucher is Read-Only</p>
                    <p class="text-xs text-gray-500">This voucher {{ $voucher->used_count > 0 ? 'has customer usage' : 'is expired' }}. No changes can be made.</p>
                </div>
            </div>
            <a href="{{ route('admin.vouchers.index') }}"
                class="px-5 py-2.5 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold transition-colors">
                Back to List
            </a>
        </div>
    @endif
</form>

<script>
    const codeInput = document.getElementById('code');
    const typeSelect = document.getElementById('discount_type');
    const valueInput = document.getElementById('discount_value');
    const productSelect = document.getElementById('product_id');
    const prevCode = document.getElementById('prev-code');
    const prevVal = document.getElementById('prev-val');
    const prevProduct = document.getElementById('prev-product');
    const valueSuffix = document.getElementById('value-suffix');

    function updateValueSuffix() {
        const t = typeSelect.value;
        if (valueSuffix) {
            valueSuffix.textContent = t === 'percentage' ? '%' : '$';
        }
        updatePreview();
    }

    function updatePreview() {
        const code = codeInput.value.trim().toUpperCase() || '—';
        const val = valueInput.value;
        const type = typeSelect.value;
        const prod = productSelect.options[productSelect.selectedIndex].text;

        prevCode.textContent = code;
        if (val) {
            prevVal.textContent = type === 'percentage' ? val + '%' : parseFloat(val).toFixed(2) + '$';
        } else {
            prevVal.textContent = '—';
        }
        prevProduct.textContent = productSelect.value ? prod.split(' ($')[0] : 'Any product';
    }

    // Generate random code via AJAX
    document.getElementById('generate-btn').addEventListener('click', async () => {
        const btn = document.getElementById('generate-btn');
        btn.disabled = true;
        btn.textContent = '…';
        try {
            const res = await fetch('{{ route('admin.vouchers.generate-code') }}');
            const data = await res.json();
            codeInput.value = data.code;
            updatePreview();
        } catch (e) {
            console.error(e);
        } finally {
            btn.disabled = false;
            btn.innerHTML =
                `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Generate`;
        }
    });

    // Live preview listeners
    [codeInput, typeSelect, valueInput, productSelect].forEach(el => el.addEventListener('input', updatePreview));
    codeInput.addEventListener('input', () => {
        codeInput.value = codeInput.value.toUpperCase();
    });
    updatePreview(); // run on page load
    updateValueSuffix();
</script>
