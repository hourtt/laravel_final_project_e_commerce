 <h1 class="text-3xl font-bold text-gray-900 mb-8 tracking-tight">Shopping Cart</h1>
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
     {{-- ═══════════════════════════════════════
                LEFT COLUMN — Cart Table
                ═══════════════════════════════════════ --}}
     <div class="lg:col-span-2">
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

                     {{-- ── ROW SUBTOTAL ODOMETER ───────────────────────────────
                          Driven by the 'row-price-updated-{id}' custom event.
                          Uses drumStyle(d) for instant-snap on load, animated
                          on updates.
                     ──────────────────────────────────────────────────────────── --}}
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

             {{-- Voucher --}}
             <div class="mb-6" id="voucher-section">
                 {{-- Input row (hidden when a voucher is applied) --}}
                 <div class="flex items-center gap-2" id="voucher-input-row"
                     style="{{ $voucherCode ? 'display:none' : '' }}">
                     <input type="text" id="voucher-input" placeholder="Discount voucher" value=""
                         class="voucher-input flex-1 h-10 px-4 rounded-xl border border-gray-200 text-sm focus:border-gray-400 focus:ring-0 outline-none transition-colors font-mono uppercase tracking-widest">
                     <button type="button" id="voucher-apply-btn" onclick="applyVoucher()"
                         class="h-10 px-4 rounded-xl bg-gray-900 hover:bg-gray-700 text-white text-sm font-bold transition-colors whitespace-nowrap">
                         Apply
                     </button>
                 </div>

                 {{-- Applied voucher badge (visible when active) --}}
                 <div id="voucher-applied-row"
                     class="flex items-center justify-between px-3 py-2 rounded-xl bg-emerald-50 border border-emerald-200"
                     style="{{ $voucherCode ? '' : 'display:none' }}">
                     <div class="flex items-center gap-2">
                         <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                             <path fill-rule="evenodd"
                                 d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                 clip-rule="evenodd" />
                         </svg>
                         <span class="text-xs font-bold text-emerald-700 font-mono tracking-widest"
                             id="applied-code-label">{{ $voucherCode ?? '' }}</span>
                     </div>
                     <button type="button" onclick="removeVoucher()"
                         class="text-xs text-gray-400 hover:text-red-500 font-semibold transition-colors">
                         Remove
                     </button>
                 </div>

                 {{-- Error message --}}
                 <p id="voucher-error" class="mt-1.5 text-xs text-red-500 hidden"></p>
             </div>

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
