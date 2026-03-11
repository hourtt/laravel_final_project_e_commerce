<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Premium Tech Store</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-sans antialiased bg-white text-gray-900">
    <div class="min-h-screen flex flex-col">

        {{-- ===================================================== --}}
        {{-- NAVIGATION BAR --}}
        {{-- ===================================================== --}}
        <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative flex items-center justify-center h-16">

                    {{-- ---- CENTER: Logo ---- --}}
                    <a href="{{ route('home') }}" class="absolute left-1/2 -translate-x-1/2 flex items-center group">
                        <x-brand-logo />
                    </a>

                    {{-- ---- RIGHT: Icons ---- --}}
                    <div class="absolute right-0 flex items-center gap-1 sm:gap-2">

                        {{-- Shopping Bag (Cart) --}}
                        @auth
                            <a href="{{ route('checkout') }}"
                                class="relative flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 transition-colors group"
                                aria-label="Shopping Cart">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-[22px] h-[22px] text-gray-500 group-hover:text-black transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                                </svg>
                                {{-- Cart count badge — stored in session --}}
                                @php $cartCount = session('cart') ? count(session('cart')) : 0; @endphp
                                @if ($cartCount > 0)
                                    <span
                                        class="absolute -top-0.5 -right-0.5 bg-blue-600 text-white text-[10px] font-bold min-w-[18px] h-[18px] px-1 rounded-full flex items-center justify-center leading-none shadow-sm">
                                        {{ $cartCount > 99 ? '99+' : $cartCount }}
                                    </span>
                                @endif
                            </a>

                            {{-- My Orders --}}
                            <a href="{{ route('user.orders') }}"
                                class="relative flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 transition-colors group"
                                aria-label="My Orders">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-[22px] h-[22px] text-gray-500 group-hover:text-black transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                </svg>
                            </a>
                        @endauth

                        {{-- Profile or Login --}}
                        @auth
                            <a href="{{ route('profile.edit') }}"
                                class="relative flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 transition-colors group"
                                aria-label="My Profile">
                                {{-- Avatar with first letter --}}
                                <div
                                    class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                                    <span class="text-white text-xs font-bold leading-none">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                </div>
                                {{-- Online dot --}}
                                <span
                                    class="absolute bottom-1.5 right-1.5 w-2 h-2 bg-emerald-400 border-2 border-white rounded-full"></span>
                            </a>

                            {{-- Logout Button (Power Icon) --}}
                            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                                @csrf
                            </form>
                            <button type="button" onclick="confirmLogout(event)"
                                class="flex items-center justify-center w-10 h-10 rounded-xl text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all group"
                                aria-label="Sign Out">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-[22px] h-[22px] transition-transform group-hover:scale-110" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                                </svg>
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Login
                            </a>
                        @endauth
                    </div>

                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>

    {{-- ======================================================= --}}
    {{-- GUEST CART LOGIN MODAL --}}
    {{-- ======================================================= --}}
    <div id="guestCartModal" class="fixed inset-0 z-[100] hidden items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity opacity-0"
            id="guestCartModalBackdrop" onclick="closeGuestCartModal()"></div>

        <!-- Modal Panel -->
        <div class="relative bg-white rounded-3xl shadow-2xl w-[90%] max-w-sm p-8 text-center transform scale-95 opacity-0 transition-all duration-300"
            id="guestCartModalPanel">

            <!-- Close Button -->
            <button onclick="closeGuestCartModal()"
                class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Icon -->
            <div
                class="w-16 h-16 bg-blue-50 text-black rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>

            <!-- Text -->
            <h3 class="text-xl font-bold text-gray-900 mb-2">Login Required</h3>
            <p class="text-gray-500 text-sm mb-8 leading-relaxed">Please login or register to add items to your cart.
            </p>

            <!-- Actions -->
            <div class="flex flex-col gap-3">
                <a href="{{ route('login') }}"
                    class="w-full py-3 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                    Log In
                </a>
                <a href="{{ route('register') }}"
                    class="w-full py-3 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-bold hover:border-blue-300 hover:bg-blue-50 transition-colors">
                    Create an account
                </a>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- JS CART LOGIC --}}
    {{-- ======================================================= --}}
    <script>
        // Guest Modal Logic
        function showGuestCartModal() {
            const modal = document.getElementById('guestCartModal');
            const panel = document.getElementById('guestCartModalPanel');
            const backdrop = document.getElementById('guestCartModalBackdrop');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Animate in
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeGuestCartModal() {
            const modal = document.getElementById('guestCartModal');
            const panel = document.getElementById('guestCartModalPanel');
            const backdrop = document.getElementById('guestCartModalBackdrop');

            // Animate out
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            panel.classList.remove('scale-100', 'opacity-100');
            panel.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300); // Wait for transition
        }

        @auth
        // Authenticated Cart Logic
        function addToCart(productId, quantity = 1) {
            fetch(`{{ url('/cart/add') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Added to cart!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.message || 'Error adding to cart.',
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Something went wrong.');
                });
        }
        @endauth
    </script>

    <script>
        /** Logout Confirmation – SweetAlert2 */
        function confirmLogout(event) {
            Swal.fire({
                title: 'Sign Out',
                text: "Are you sure you want to sign out from VoltMart?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb', // Blue-600
                cancelButtonColor: '#ef4444', // Red-500
                confirmButtonText: 'Yes, Sign Out',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                color: '#111827',
                iconColor: '#f59e0b',
                customClass: {
                    title: 'text-xl font-bold font-sans',
                    popup: 'rounded-3xl shadow-2xl border border-gray-100 p-4',
                    confirmButton: 'px-6 py-3 rounded-xl font-bold text-sm transition-all',
                    cancelButton: 'px-6 py-3 rounded-xl font-bold text-sm transition-all'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
</body>

</html>
