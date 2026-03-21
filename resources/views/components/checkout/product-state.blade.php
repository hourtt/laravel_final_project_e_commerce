 <h1 class="text-3xl font-bold text-gray-900 mb-8 tracking-tight">Shopping Cart</h1>
 @php $appliedVouchers = $appliedVouchers ?? []; @endphp
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
     <div class="lg:col-span-2 space-y-8">
         {{-- Item in cart section --}}
         <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

             {{-- Column headers --}}
             <div
                 class="grid grid-cols-[1fr_140px_110px_44px] gap-4 items-center px-6 py-3.5 border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                 <span>Product</span>
                 <span class="text-center">Quantity</span>
                 <span class="text-right">Total</span>
                 <span></span>
             </div>

             {{-- Cart rows --}}
             @foreach ($cartData as $item)
                 @php $product = $item['product']; @endphp
                 <div
                     class="grid grid-cols-[1fr_140px_110px_44px] gap-4 items-center px-6 py-5 border-b border-gray-50 last:border-b-0 hover:bg-gray-50/50 transition-colors">

                     {{-- Product info --}}
                     <div class="flex items-center gap-4 min-w-0">
                         <div
                             class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                             @php $imageUrl = $product->image_url; @endphp
                             @if ($imageUrl)
                                 <img src="{{ $imageUrl }}" class="w-full h-full object-contain mix-blend-multiply"
                                     alt="{{ $product->name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                 <span class="text-2xl hidden items-center justify-center w-full h-full">📦</span>
                             @else
                                 <span class="text-2xl flex items-center justify-center w-full h-full">📦</span>
                             @endif
                         </div>
                         <div class="min-w-0">
                             <p class="text-sm font-semibold text-gray-900 leading-snug truncate">
                                 {{ $product->name }}
                             </p>
                             <p class="text-xs text-gray-400 mt-0.5">{{ $product->category->name ?? '' }}
                             </p>
                             <p class="text-xs font-semibold text-gray-500 mt-1">
                                 ${{ number_format($product->price, 2) }} each</p>

                             {{-- ── VOUCHER PER ITEM ── --}}
                             <div class="mt-3" id="voucher-section-{{ $product->id }}">
                                 @php
                                     $itemVoucher = $appliedVouchers[$product->id] ?? null;
                                 @endphp

                                 {{-- Apply Input (Hidden if voucher is present) --}}
                                 <div class="flex items-center gap-2 max-w-[180px]"
                                     id="voucher-input-row-{{ $product->id }}"
                                     style="{{ $itemVoucher ? 'display:none' : '' }}">
                                     <input type="text" id="voucher-input-{{ $product->id }}" placeholder="Voucher"
                                         class="w-full h-8 px-3 rounded-lg border border-gray-100 text-[10px] font-mono tracking-widest focus:border-gray-300 focus:ring-0 outline-none transition-all"
                                         onkeydown="if(event.key==='Enter') applyVoucher({{ $product->id }})">
                                     <button type="button" onclick="applyVoucher({{ $product->id }})"
                                         id="voucher-apply-btn-{{ $product->id }}"
                                         class="h-8 px-3 rounded-lg bg-gray-900 hover:bg-black text-white text-[10px] font-bold transition-all">
                                         Apply
                                     </button>
                                 </div>

                                 {{-- Applied Badge (Visible if voucher is present) --}}
                                 <div id="voucher-applied-row-{{ $product->id }}"
                                     class="flex items-center justify-between gap-2 px-2 py-1 rounded-lg bg-emerald-50 border border-emerald-100 max-w-[180px]"
                                     style="{{ $itemVoucher ? '' : 'display:none' }}">
                                     <div class="flex items-center gap-1.5 min-w-0">
                                         <svg class="w-3 h-3 text-emerald-500 flex-shrink-0" fill="currentColor"
                                             viewBox="0 0 20 20">
                                             <path fill-rule="evenodd"
                                                 d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                 clip-rule="evenodd" />
                                         </svg>
                                         <span
                                             class="text-[10px] font-bold text-emerald-700 font-mono tracking-wider truncate"
                                             id="applied-code-label-{{ $product->id }}">{{ $itemVoucher['code'] ?? '' }}</span>
                                     </div>
                                     <button type="button" onclick="removeVoucher({{ $product->id }})"
                                         class="text-[10px] text-gray-400 hover:text-red-500 font-semibold flex-shrink-0">
                                         ✕
                                     </button>
                                 </div>

                                 {{-- Flat discount indicator --}}
                                 @if ($itemVoucher)
                                     <p class="text-[10px] text-emerald-600 font-bold mt-1"
                                         id="voucher-discount-label-{{ $product->id }}">
                                         -${{ number_format($itemVoucher['discount'], 2) }} discount
                                     </p>
                                 @else
                                     <p class="text-[10px] text-emerald-600 font-bold mt-1 hidden"
                                         id="voucher-discount-label-{{ $product->id }}"></p>
                                 @endif

                                 <p id="voucher-error-{{ $product->id }}" class="mt-1 text-[9px] text-red-500 hidden">
                                 </p>
                             </div>
                         </div>
                     </div>

                     {{-- Qty stepper --}}
                     <div class="flex items-center justify-center gap-2">
                         <button type="button" id="btn-minus-{{ $product->id }}" class="qty-btn"
                             onclick="changeQty({{ $product->id }}, -1)"
                             {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>−</button>
                         <span id="qty-{{ $product->id }}"
                             class="w-7 text-center text-sm font-bold text-gray-900 tabular-nums"
                             data-price="{{ $product->price }}" data-max="{{ $product->stock }}"
                             data-min="1">{{ $item['quantity'] }}</span>
                         <button type="button" id="btn-plus-{{ $product->id }}" class="qty-btn"
                             onclick="changeQty({{ $product->id }}, 1)"
                             {{ $item['quantity'] >= $product->stock ? 'disabled' : '' }}>+</button>
                     </div>

                     {{-- ROW SUBTOTAL ODOMETER --}}
                     <div class="text-right" x-data="priceOdometer({{ $item['subtotal'] }})"
                         x-on:row-price-updated-{{ $product->id }}.window="onPriceUpdated($event.detail)">
                         <div class="odometer-wrapper inline-flex items-baseline gap-0 text-sm font-bold leading-none select-none"
                             :class="flashColor">
                             <span>$</span>
                             <template x-for="(d, i) in intDigits" :key="'ri-' + i">
                                 <span class="odometer-slot">
                                     <span class="odometer-drum" :style="drumStyle(d)">
                                         <template x-for="n in 10" :key="n">
                                             <span class="odometer-digit" x-text="n - 1"></span>
                                         </template>
                                     </span>
                                 </span>
                             </template>
                             <span>.</span>
                             <template x-for="(d, i) in decDigits" :key="'rd-' + i">
                                 <span class="odometer-slot">
                                     <span class="odometer-drum" :style="drumStyle(d)">
                                         <template x-for="n in 10" :key="n">
                                             <span class="odometer-digit" x-text="n - 1"></span>
                                         </template>
                                     </span>
                                 </span>
                             </template>
                         </div>
                     </div>

                     {{-- Remove button --}}
                     <div class="flex justify-end">
                         <form action="{{ route('cart.remove', $product->id) }}" method="POST" class="inline">
                             @csrf @method('DELETE')
                             <button type="submit"
                                 class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all"
                                 title="Remove item">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20"
                                     fill="currentColor">
                                     <path fill-rule="evenodd"
                                         d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                         clip-rule="evenodd" />
                                 </svg>
                             </button>
                         </form>
                     </div>
                 </div>
             @endforeach

         </div>

         {{-- ── Shipping Address Section ── --}}
         <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8" x-data="{ selectedAddress: {{ count($addresses) > 0 ? $addresses->where('is_default', true)->first()->id ?? $addresses->first()->id : 'null' }}, showNewForm: {{ count($addresses) === 0 ? 'true' : 'false' }} }">
             <div class="flex items-center justify-between mb-6">
                 <h2 class="text-xl font-bold text-gray-900 border-l-4 border-blue-600 pl-4">Shipping Address</h2>
                 <button @click="showNewForm = !showNewForm"
                     class="text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors flex items-center gap-2">
                     <span x-text="showNewForm ? '✕ Cancel' : '+ Add New Address'"></span>
                 </button>
             </div>

             @if (count($addresses) > 0)
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6" x-show="!showNewForm">
                     @foreach ($addresses as $address)
                         <label class="relative cursor-pointer group">
                             <input type="radio" name="selected_address_id" value="{{ $address->id }}"
                                 class="sr-only" x-model="selectedAddress">
                             <div class="h-full p-5 rounded-2xl border-2 transition-all group-hover:shadow-md"
                                 :class="selectedAddress == '{{ $address->id }}' ? 'border-blue-600 bg-blue-50/30' :
                                     'border-gray-100 bg-white hover:border-blue-200'">

                                 <div class="flex justify-between items-start mb-2">
                                     <div class="flex items-center gap-2">
                                         <h4 class="font-bold text-gray-900 text-sm truncate max-w-[150px]">
                                             {{ $address->full_name }}</h4>
                                         <span
                                             class="px-2 py-0.5 rounded text-[9px] font-bold uppercase transition-colors {{ $address->address_type === 'Home' ? 'bg-orange-50 text-orange-600' : 'bg-purple-50 text-purple-600' }}">
                                             {{ $address->address_type }}
                                         </span>
                                     </div>
                                     <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                         :class="selectedAddress == '{{ $address->id }}' ?
                                             'border-blue-600 bg-blue-600 text-white' : 'border-gray-200 bg-white'">
                                         <svg x-show="selectedAddress == '{{ $address->id }}'" class="w-3.5 h-3.5"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                 d="M5 13l4 4L19 7" />
                                         </svg>
                                     </div>
                                 </div>

                                 <div class="text-[11px] text-gray-500 space-y-1">
                                     <p class="flex items-center gap-1.5 font-medium">
                                         <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                 d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                         </svg>
                                         {{ $address->phone_number }}
                                     </p>
                                     <p class="leading-relaxed opacity-80">{{ $address->street_address }},
                                         {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }},
                                         {{ $address->country }}</p>
                                 </div>

                                 @if ($address->is_default)
                                     <div
                                         class="mt-3 flex items-center gap-1 text-[9px] font-bold text-blue-600 uppercase tracking-widest">
                                         <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                             <path
                                                 d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                         </svg>
                                         Default Address
                                     </div>
                                 @endif
                             </div>
                         </label>
                     @endforeach
                 </div>
             @endif

             <div x-show="showNewForm" x-collapse>
                 <div class="p-6 bg-gray-50/50 rounded-2xl border border-gray-100">
                     @include('profile.partials.address_form', ['addresses' => []]) {{-- Empty addresses to hide listing within include --}}
                 </div>
             </div>
         </div>
     </div>

     {{-- ═══════════════════════════════════════
                RIGHT COLUMN — Order Summary
                ═══════════════════════════════════════ --}}
     @php
         $delivery = 0.0;
         $voucherDiscount = $voucherDiscount ?? 0.0;
         $voucherCode = $voucherCode ?? null;
         $finalTotal = $discountedTotal ?? $total - $voucherDiscount;
     @endphp

     <div class="lg:col-span-1">
         <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-7 sticky top-24">
             <h2 class="text-sm font-bold text-gray-900 mb-5 tracking-wide uppercase">Order Summary</h2>

             {{-- Breakdown --}}
             <div class="space-y-3 text-sm mb-6">
                 <div class="flex items-center justify-between text-gray-500">
                     <span>Sub Total</span>
                     {{-- Plain text — updated by JS via id="subtotal-display" --}}
                     <span id="subtotal-display" class="font-semibold text-gray-800">
                         ${{ number_format($total, 2) }} USD
                     </span>
                 </div>
                 {{-- Discount row: shown only when voucher is active --}}
                 <div class="flex items-center justify-between text-gray-500" id="discount-row"
                     style="{{ $voucherDiscount > 0 ? '' : 'display:none' }}">
                     <span>Discount</span>
                     <span id="discount-display" class="font-semibold text-emerald-600">
                         -${{ number_format($voucherDiscount, 2) }} USD
                     </span>
                 </div>
                 <div class="flex items-center justify-between text-gray-500">
                     <span>Delivery fee</span>
                     <span class="font-semibold text-gray-800">Free</span>
                 </div>
             </div>

             {{-- ── GRAND TOTAL ODOMETER ──────────────────────────────────
                  Listens to the global 'price-updated' event from changeQty().
             ─────────────────────────────────────────────────────────────── --}}
             <div class="border-t border-gray-100 pt-5 mb-6">
                 <div class="flex items-center justify-between">
                     <span class="text-sm font-semibold text-gray-900">Total</span>

                     <div x-data="priceOdometer({{ $finalTotal }})" x-on:price-updated.window="onPriceUpdated($event.detail)"
                         class="odometer-wrapper flex items-baseline gap-0 font-bold text-[1.4rem] leading-none select-none"
                         :class="flashColor">
                         <span class="mr-[1px]">$</span>

                         <template x-for="(d, i) in intDigits" :key="'int-' + i">
                             <span class="odometer-slot">
                                 <span class="odometer-drum" :style="drumStyle(d)">
                                     <template x-for="n in 10" :key="n">
                                         <span class="odometer-digit" x-text="n - 1"></span>
                                     </template>
                                 </span>
                             </span>
                         </template>

                         <span class="mx-[1px]">.</span>

                         <template x-for="(d, i) in decDigits" :key="'dec-' + i">
                             <span class="odometer-slot">
                                 <span class="odometer-drum" :style="drumStyle(d)">
                                     <template x-for="n in 10" :key="n">
                                         <span class="odometer-digit" x-text="n - 1"></span>
                                     </template>
                                 </span>
                             </span>
                         </template>

                         <span class="ml-2 text-sm font-semibold opacity-50">USD</span>
                     </div>
                 </div>
             </div>

             {{-- Checkout Now (ABA PayWay form) --}}
             <div class="mt-8">
                 <form method="POST" target="aba_webservice"
                     action="https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase"
                     id="aba_merchant_request">
                     <input type="hidden" name="hash" value="{{ $hash ?? '' }}" id="hash" />
                     <input type="hidden" name="tran_id" value="{{ $tranId ?? '' }}" id="tran_id" />
                     <input type="hidden" name="amount" value="{{ $amount ?? '' }}" id="amount" />
                     <input type="hidden" name="payment_option" value="{{ $payment_option ?? '' }}"
                         id="payment_option" />
                     <input type="hidden" name="merchant_id" value="{{ $merchant_id ?? '' }}" id="merchant_id" />
                     <input type="hidden" name="req_time" value="{{ $req_time ?? '' }}" id="req_time" />
                     <input type="hidden" name="currency" value="{{ $currency ?? '' }}" id="currency" />
                     <input type="hidden" name="return_url" value="{{ $return_url ?? '' }}" id="return_url" />
                     <input type="hidden" name="continue_success_url" value="{{ $continue_success_url ?? '' }}"
                         id="continue_success_url" />

                     <button type="button" id="checkout_button"
                         class="w-full relative flex items-center justify-center py-4 bg-black hover:bg-gray-800 active:scale-[0.98] text-white text-sm font-bold rounded-2xl transition-all shadow-lg shadow-black/10 tracking-wide">
                         <span id="checkout_text">Checkout Now</span>
                     </button>
                 </form>
             </div>
         </div>
     </div>

 </div>{{-- /grid --}}
