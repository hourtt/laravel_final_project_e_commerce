  <header class="bg-white border-b border-gray-100 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-6 lg:px-8 h-16 flex items-center justify-between">
          <a href="{{ route('home') }}">
              <x-brand-logo />
          </a>
          <div class="flex items-center gap-3">
              <a href="{{ route('home') }}"
                  class="hidden sm:inline-flex items-center px-4 py-1.5 rounded-full border border-gray-300 text-sm font-medium text-gray-700 hover:border-gray-800 hover:text-gray-900 transition-colors">
                  Back to Shop
              </a>
          </div>
      </div>
  </header>
