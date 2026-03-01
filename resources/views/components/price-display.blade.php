@props(['value', 'id' => 'grand-total', 'size' => 'text-sm', 'currency' => 'USD'])

<div x-data="priceOdometer({{ $value }})" x-on:{{ $id }}-updated.window="onPriceUpdated($event.detail)"
    class="odometer-wrapper flex items-baseline gap-0 leading-none select-none {{ $size }}"
    :class="flashColor">

    <span class="mr-[1px]">$</span>

    {{-- Integer Digits --}}
    <template x-for="(d, i) in intDigits" :key="'int-' + i">
        <span class="odometer-slot">
            <span class="odometer-drum"
                :style="`transform: translateY(${-(d * 1.35)}em); transition: transform 0.55s cubic-bezier(0.23, 1, 0.32, 1)`">
                <template x-for="n in 10" :key="n">
                    <span class="odometer-digit" x-text="n - 1"></span>
                </template>
            </span>
        </span>
    </template>

    <span class="mx-[1px]">.</span>

    {{-- Decimal Digits --}}
    <template x-for="(d, i) in decDigits" :key="'dec-' + i">
        <span class="odometer-slot">
            <span class="odometer-drum"
                :style="`transform: translateY(${-(d * 1.35)}em); transition: transform 0.55s cubic-bezier(0.23, 1, 0.32, 1)`">
                <template x-for="n in 10" :key="n">
                    <span class="odometer-digit" x-text="n - 1"></span>
                </template>
            </span>
        </span>
    </template>

    @if ($currency)
        <span class="ml-2 text-xs font-semibold opacity-50">{{ $currency }}</span>
    @endif
</div>
