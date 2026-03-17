<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Shopping Cart' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @include('components.checkout.checkout-style')

    {{--
        priceOdometer — plain global function so x-data="priceOdometer(value)"
        resolves reliably via Alpine's expression evaluator without the
        Alpine.data() argument-passing quirks found in Vite/module setups.

        _ready + $nextTick strategy:
          • On first render: _ready=false → drumStyle() returns transition:none
            → drums SNAP to the correct digit position with zero animation
            (prevents the 0→N boot flash that made prices appear wrong)
          • After $nextTick: _ready=true → subsequent quantity changes animate
            smoothly with cubic-bezier easing
    --}}
    <script>
        function priceOdometer(initialPrice) {
            return {
                price: initialPrice,
                flashColor: '',
                _flashTimer: null,
                _ready: false, // true after first DOM paint

                init() {
                    // Mark ready only AFTER Alpine has completed the first
                    // paint so the initial drum positions are snapped, not
                    // animated from 0.
                    this.$nextTick(() => {
                        this._ready = true;
                    });
                },

                // computed digit arrays
                get intDigits() {
                    return Math.floor(Math.abs(this.price || 0))
                        .toString().split('').map(Number);
                },

                get decDigits() {
                    const dec = (this.price || 0).toFixed(2).split('.')[1] ?? '00';
                    return dec.split('').map(Number);
                },

                // Full inline style for one drum span.
                // transition:none before first paint, smooth after.
                drumStyle(d) {
                    const trans = this._ready ?
                        'transform 0.52s cubic-bezier(0.23, 1, 0.32, 1)' :
                        'none';
                    return `transform: translateY(${-(d * 1.35)}em); transition: ${trans}`;
                },

                // called by vanilla-JS quantity controls
                onPriceUpdated({
                    total,
                    direction
                }) {
                    this.price = parseFloat(total) || 0;

                    this.flashColor = direction === 'up' ?
                        'text-emerald-500' :
                        'text-rose-500';

                    clearTimeout(this._flashTimer);
                    this._flashTimer = setTimeout(() => {
                        this.flashColor = '';
                    }, 700);
                }
            };
        }
    </script>
</head>

<body class="antialiased">
    @include('components.checkout.navbar')

    <main class="max-w-7xl mx-auto px-6 lg:px-8 py-10">
        <div id="toast-container"></div>
        {{-- Toast Flash Messages --}}
        @foreach (['error', 'success', 'warning'] as $type)
            @if (session($type))
                <div class="server-toast hidden" data-type="{{ $type }}">{{ session($type) }}</div>
            @endif
        @endforeach

        {{-- Main Page Content Slot --}}
        {{ $slot }}
    </main>

    @include('components.checkout.checkout-script')
</body>

</html>
