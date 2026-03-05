<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <!-- Noto Sans Kaithi – stat numbers -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Kaithi&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Noto Sans Kaithi – dashboard stat numbers ── */
        .font-stat {
            font-family: 'Noto Sans Kaithi', sans-serif;
        }

        /* ── Active link ── */
        .sidebar-link.active {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white;
            box-shadow: 0 4px 15px -3px rgba(37, 99, 235, 0.4);
        }

        .sidebar-link.active svg {
            color: white;
        }

        /* ── Sidebar transition ── */
        #admin-sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: width;
        }

        #main-content {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: margin-left;
        }

        /* ── Label fade ── */
        .nav-label {
            transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            overflow: hidden;
            max-width: 150px;
            opacity: 1;
        }

        #admin-sidebar.collapsed .nav-label {
            opacity: 0;
            max-width: 0;
        }

        .section-label {
            transition: opacity 0.2s ease, max-height 0.3s ease, margin 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
            max-height: 20px;
        }

        #admin-sidebar.collapsed .section-label {
            opacity: 0;
            max-height: 0;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .user-info {
            transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            overflow: hidden;
            max-width: 150px;
            opacity: 1;
        }

        #admin-sidebar.collapsed .user-info {
            opacity: 0;
            max-width: 0;
            margin-left: 0;
        }

        /* ── Tooltip for collapsed state ── */
        .sidebar-link {
            position: relative;
        }

        #admin-sidebar.collapsed .sidebar-link .tooltip {
            display: block;
        }

        .tooltip {
            display: none;
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            background: #1e293b;
            color: #f1f5f9;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 99;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .tooltip::before {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #1e293b;
        }

        /* Sign-out button tooltip */
        #admin-sidebar.collapsed .signout-btn .tooltip {
            display: block;
        }

        .signout-btn {
            position: relative;
        }

        /* Toggle button rotation */
        #sidebar-toggle svg {}

        /* Point right (expand) when sidebar is collapsed */
        #admin-sidebar.collapsed #sidebar-toggle svg {
            transform: rotate(180deg);
        }

        /* ── Logo header: always a clean single row, just the logo ── */
        #sidebar-logo-header {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 4rem;
            padding: 0 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
            overflow: hidden;
        }

        /* Logo link — always visible, shrinks gap when collapsed */
        #sidebar-logo-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            overflow: hidden;
            white-space: nowrap;
            transition: gap 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #admin-sidebar.collapsed #sidebar-logo-link {
            gap: 0;
        }

        /* “VoltMart” text: slides out smoothly */
        .logo-text {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            max-width: 160px;
            opacity: 1;
            transition: max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.2s ease;
        }

        #admin-sidebar.collapsed .logo-text {
            max-width: 0;
            opacity: 0;
        }

        /* Profile avatar centering when collapsed */
        #admin-sidebar.collapsed .profile-row {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        /* ── Top-bar toggle button ── */
        #sidebar-toggle {
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        #sidebar-toggle svg {}

        /* Chevron flips to point right when collapsed */
        body.sidebar-collapsed #sidebar-toggle svg {
            transform: rotate(180deg);
        }
    </style>
</head>

<body class="font-sans antialiased bg-slate-50 text-gray-900">
    <div class="flex min-h-screen">

        <!-- ===================== SIDEBAR ===================== -->
        <aside id="admin-sidebar"
            class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 flex flex-col shadow-2xl overflow-hidden">

            <!-- Logo only (toggle moved to top bar) -->
            <div id="sidebar-logo-header">
                <a id="sidebar-logo-link" href="{{ route('admin.dashboard') }}"
                    class="hover:opacity-80 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-indigo-400 flex-shrink-0"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z" />
                    </svg>
                    <span class="logo-text font-bold text-white text-base tracking-wide">VoltMart</span>
                </a>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto overflow-x-hidden">
                <p class="section-label text-slate-500 text-[10px] font-bold uppercase tracking-widest px-3 mb-3">Main
                </p>

                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all duration-200 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400 flex-shrink-0"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                    </svg>
                    <span class="nav-label">Dashboard</span>
                    <span class="tooltip">Dashboard</span>
                </a>

                <p class="section-label text-slate-500 text-[10px] font-bold uppercase tracking-widest px-3 mt-5 mb-3">
                    Management</p>

                <a href="{{ route('admin.products.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all duration-200 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400 flex-shrink-0"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="nav-label">Products</span>
                    <span class="tooltip">Products</span>
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all duration-200 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400 flex-shrink-0"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                    </svg>
                    <span class="nav-label">Categories</span>
                    <span class="tooltip">Categories</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all duration-200 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400 flex-shrink-0"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                    <span class="nav-label">Users</span>
                    <span class="tooltip">Users</span>
                </a>

                <a href="{{ route('admin.orders.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all duration-200 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400 flex-shrink-0"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                    </svg>
                    <span class="nav-label">Orders</span>
                    <span class="tooltip">Orders</span>
                </a>

                <a href="{{ route('admin.vouchers.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition-all duration-200 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400 flex-shrink-0"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 100 4v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 100-4V6z" />
                    </svg>
                    <span class="nav-label">Vouchers</span>
                    <span class="tooltip">Vouchers</span>
                </a>
            </nav>

            <!-- Bottom: profile + logout -->
            <div class="px-3 py-4 border-t border-white/10 flex-shrink-0">
                <div
                    class="profile-row flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 transition-colors cursor-pointer mb-1">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span
                            class="text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                    </div>
                    <div class="user-info flex-1 min-w-0 nav-label">
                        <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-slate-400 text-[10px] truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="signout-btn w-full flex items-center gap-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-red-500/20 hover:text-red-400 transition-all duration-200 text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="nav-label">Sign Out</span>
                        <span class="tooltip">Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ===================== MAIN CONTENT ===================== -->
        <div id="main-content" class="flex-1 ml-64 flex flex-col min-h-screen">

            <!-- Top Bar -->
            <header class="sticky top-0 z-30 bg-white border-b border-gray-100 shadow-sm">
                <div class="px-6 h-16 flex items-center justify-between gap-4">

                    <!-- LEFT: Toggle button + title -->
                    <div class="flex items-center gap-3">
                        <!-- Sidebar toggle: lives here in the top bar -->
                        <button id="sidebar-toggle"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500
                                   hover:bg-gray-100 hover:text-gray-800 transition-colors flex-shrink-0"
                            title="Toggle sidebar">
                            <!-- Hamburger / chevron icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <!-- Thin divider -->
                        <span class="w-px h-5 bg-gray-200 flex-shrink-0"></span>
                        <div>
                            <h1 class="text-base font-bold text-gray-900">Admin Dashboard</h1>
                            <p class="text-xs text-gray-400">{{ now()->format('l, F j, Y | g:i A') }}</p>
                        </div>
                    </div>

                    <!-- RIGHT: Visit Store -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-50
                                   text-blue-600 text-xs font-semibold hover:bg-blue-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                <path
                                    d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                            </svg>
                            View Store
                        </a>
                    </div>
                </div>
            </header>

            <!-- Page Slot -->
            <main class="flex-1 p-8">
                <!-- Global Flash Messages -->
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show"
                        class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 flex-shrink-0"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-semibold">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show"
                        class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 flex-shrink-0"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-semibold">{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-red-500 hover:text-red-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')

    <script>
        (function() {
            const STORAGE_KEY = 'admin_sidebar_collapsed';
            const sidebar = document.getElementById('admin-sidebar');
            const content = document.getElementById('main-content');
            const toggleBtn = document.getElementById('sidebar-toggle');

            const EXPANDED_W = '16rem'; // w-64
            const COLLAPSED_W = '4rem'; // w-16

            function applyState(collapsed, animate) {
                if (!animate) {
                    sidebar.style.transition = 'none';
                    content.style.transition = 'none';
                }

                if (collapsed) {
                    sidebar.style.width = COLLAPSED_W;
                    content.style.marginLeft = COLLAPSED_W;
                    sidebar.classList.add('collapsed');
                    document.body.classList.add('sidebar-collapsed');
                } else {
                    sidebar.style.width = EXPANDED_W;
                    content.style.marginLeft = EXPANDED_W;
                    sidebar.classList.remove('collapsed');
                    document.body.classList.remove('sidebar-collapsed');
                }

                if (!animate) {
                    sidebar.offsetHeight; // force reflow
                    sidebar.style.transition = '';
                    content.style.transition = '';
                }
            }

            // ── Restore on page load (no animation) ──
            const saved = localStorage.getItem(STORAGE_KEY) === 'true';
            applyState(saved, false);

            // ── Toggle on button click ──
            toggleBtn.addEventListener('click', function() {
                const nowCollapsed = !sidebar.classList.contains('collapsed');
                applyState(nowCollapsed, true);
                localStorage.setItem(STORAGE_KEY, nowCollapsed);
            });
        })();
    </script>
</body>

</html>
