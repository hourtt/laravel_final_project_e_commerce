<x-guest-layout maxWidth="xl">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Create an account</h2>
        <p class="text-sm text-gray-500 mt-2">Join Volt Mart to start shopping</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="{{ __('Full Name') }}" class="text-gray-700 font-medium" />
            <x-text-input id="name"
                class="block w-full mt-2 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="{{ __('Email Address') }}" class="text-gray-700 font-medium" />
            <x-text-input id="email"
                class="block w-full mt-2 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="{{ __('Password') }}" class="text-gray-700 font-medium" />
            <x-text-input id="password"
                class="block w-full mt-2 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="{{ __('Confirm Password') }}"
                class="text-gray-700 font-medium" />
            <x-text-input id="password_confirmation"
                class="block w-full mt-2 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="col-span-full mt-2">
            <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                {{ __('Create account') }}
            </button>
        </div>

        <div class="col-span-full mt-2 text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('login') }}"
                class="font-medium text-blue-600 hover:text-blue-500 hover:underline transition-colors focus:outline-none focus:underline">
                Sign in
            </a>
        </div>
    </form>
</x-guest-layout>