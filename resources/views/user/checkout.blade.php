<x-checkout-layout>
    <x-slot:title>
        Shopping Cart
    </x-slot:title>

    {{-- ─── CONTENT ─── --}}
    @if (empty($cartData))
        @include('components.checkout.empty-product-state')
    @else
        @include('components.checkout.product-state')
    @endif
</x-checkout-layout>
