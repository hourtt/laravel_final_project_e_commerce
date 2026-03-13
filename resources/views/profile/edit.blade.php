<x-app-layout>
    <div class="py-12 bg-gray-50/50 min-h-screen" x-data="{ activeTab: 'profiles' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Profile Hero Section -->
            <div class="relative overflow-hidden bg-white rounded-3xl shadow-sm border border-gray-100 mt-2">
                <!-- Decorative Background Blur -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-blue-600/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-indigo-600/5 rounded-full blur-3xl">
                </div>

                <div class="relative p-8 sm:p-10 flex flex-col md:flex-row items-center gap-8">
                    <!-- Profile Avatar -->
                    <div class="relative group">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full blur opacity-25 group-hover:opacity-40 transition duration-1000">
                        </div>
                        <div class="relative w-32 h-32 bg-white rounded-full p-1 shadow-inner">
                            <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=2563eb&color=fff&size=200' }}"
                                alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover">
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="text-center md:text-left flex-1">
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                            {{ $user->name }}
                        </h1>
                        <p
                            class="text-gray-500 font-medium flex items-center justify-center md:justify-start gap-2 mt-1">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                </path>
                            </svg>
                            {{ $user->email }}
                        </p>
                        <div class="flex items-center justify-center md:justify-start gap-3 mt-4">
                            <span
                                class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $user->role }}
                            </span>
                            <span class="text-xs text-gray-400 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Joined {{ $user->created_at->format('M Y') }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Link (Hidden for Admins) -->
                    @if ($user->role !== 'admin')
                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="{{ route('user.orders') }}"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-all duration-200 shadow-lg shadow-blue-600/20 active:scale-95">
                                View Order History
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!--  Dashboard Stats Grid (Hidden for Admins) -->
            @if ($user->role !== 'admin')
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Stat: Total Orders -->
                    <div
                        class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center text-center">
                        <div
                            class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 transition-transform hover:scale-110 duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Orders</p>
                        <p class="text-3xl font-black text-gray-900 mt-1">{{ $totalOrders }}</p>
                    </div>

                    <!-- Stat: Total Spent -->
                    <div
                        class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center text-center">
                        <div
                            class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 transition-transform hover:scale-110 duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Spent</p>
                        <p class="text-3xl font-black text-gray-900 mt-1">${{ number_format($totalSpent, 2) }}</p>
                    </div>

                    <!-- Stat: Total Saved -->
                    <div
                        class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center text-center">
                        <div
                            class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4 transition-transform hover:scale-110 duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Money Saved</p>
                        <p class="text-3xl font-black text-amber-600 mt-1">${{ number_format($totalSaved, 2) }}</p>
                    </div>
                </div>
            @endif

            <!-- Sidebar Navigation Layout -->
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left Column: Navigation Menu -->
                <div class="w-full lg:w-72 space-y-2">
                    <button @click="activeTab = 'profiles'"
                        :class="activeTab === 'profiles' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' :
                            'bg-white text-gray-600 hover:bg-gray-50 border border-transparent'"
                        class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profiles
                    </button>

                    <button @click="activeTab = 'security'"
                        :class="activeTab === 'security' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' :
                            'bg-white text-gray-600 hover:bg-gray-50 border border-transparent'"
                        class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                        Password & Security
                    </button>

                    <div class="mt-4 border-t border-gray-100">
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                            @csrf
                        </form>
                        <button onclick="confirmLogout(event)"
                            class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm text-white bg-red-600 hover:bg-red-700 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            Sign Out
                        </button>
                    </div>
                </div>

                <!-- Right Column: Content Area -->
                <div class="flex-1">
                    <!-- Tab: Profiles -->
                    <div x-show="activeTab === 'profiles'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-8">
                            <div class="max-w-xl">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Password & Security -->
                    <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-8">
                            <div class="max-w-xl">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>

                        <!-- Danger Zone included in Security for a cleaner UI -->
                        <div class="bg-red-50/50 p-8 rounded-3xl border border-red-100 overflow-hidden relative">
                            <div
                                class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-red-500/5 rounded-full blur-2xl">
                            </div>
                            <div class="max-w-xl relative text-left">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
