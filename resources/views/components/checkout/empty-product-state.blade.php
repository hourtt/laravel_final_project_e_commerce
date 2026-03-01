 <div class="min-h-[60vh] flex flex-col items-center justify-center text-center">
     <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center text-5xl mb-6">🛒</div>
     <h2 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h2>
     <p class="text-gray-500 mb-8">Looks like you haven't added anything yet.</p>
     <a href="{{ route('home') }}"
         class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white text-sm font-semibold rounded-full hover:bg-gray-800 transition-colors">
         Start Shopping
         <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
             stroke-width="2">
             <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
         </svg>
     </a>
 </div>
