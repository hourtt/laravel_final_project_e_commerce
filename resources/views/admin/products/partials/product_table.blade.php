@forelse($products as $product)
    <tr class="hover:bg-slate-50/60 transition-colors group">

        {{-- Thumbnail --}}
        <td class="px-6 py-3">
            <div class="flex items-center justify-center">
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                        class="w-11 h-11 object-contain">
                @else
                    <div
                        class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                @endif
            </div>
        </td>

        {{-- Name + description snippet --}}
        <td class="px-4 py-3 max-w-xs align-top text-left">
            <p class="font-semibold text-gray-800 leading-tight mt-2">{{ $product->name }}</p>
            <div x-data="{ open: false, uid: {{ $product->id }} }"
                @close-descs.window="if ($event.detail.except !== uid) open = false"
                class="relative mt-0.5">
                <div class="transition-all duration-300 ease-in-out text-[11px] text-gray-400 leading-relaxed overflow-hidden relative"
                    :style="open ? 'max-height: 20rem;' : 'max-height: 1.4rem;'">
                    {{ $product->description }}
                    {{-- Soft gradient fade when collapsed to replace line-clamp; without x-transition so it appears instantly on collapse --}}
                    <div x-show="!open"
                        class="absolute bottom-0 left-0 right-0 h-4 bg-gradient-to-t from-white to-transparent pointer-events-none">
                    </div>
                </div>
                @if (strlen($product->description) > 60)
                    <button type="button"
                        @click.stop="open = !open; if (open) $dispatch('close-descs', { except: uid })"
                        class="inline-flex items-center justify-center gap-0.5 text-[10px] text-blue-500 hover:text-blue-700 font-semibold mt-1 focus:outline-none transition-colors duration-150 relative z-10 w-full bg-white/50 backdrop-blur-sm -top-1 pt-1 rounded">
                        <span x-text="open ? 'See Less' : 'See More'">See More</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-3 h-3 transition-transform duration-300"
                            :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                @endif
            </div>
        </td>

        {{-- Category --}}
        <td class="px-4 py-3 text-center">
            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full text-xs font-semibold">
                @if($product->category && $product->category->icon)
                    @if(str_contains($product->category->icon, 'images/category/'))
                        <img src="{{ asset(str_replace('public/', '', $product->category->icon)) }}" 
                             alt="" class="w-3.5 h-3.5 object-contain">
                    @else
                        <span>{{ $product->category->icon }}</span>
                    @endif
                @endif
                {{ $product->category->name ?? 'Uncategorized' }}
            </span>
        </td>

        {{-- Price --}}
        <td class="px-4 py-3 text-center text-gray-900">${{ number_format($product->price, 2) }}
        </td>

        {{-- Stock --}}
        <td class="px-4 py-3 text-center border-l-0 text-gray-900">
            @if ($product->stock <= 0)
                <span
                    class="inline-flex items-center justify-center gap-1 text-red-600 bg-red-50 px-2 py-0.5 rounded-full text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Out of Stock
                </span>
            @elseif($product->stock <= 5)
                <span
                    class="inline-flex items-center justify-center gap-1 text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Low
                    ({{ $product->stock }})
                </span>
            @else
                <span
                    class="inline-flex items-center justify-center gap-1 text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full text-xs font-semibold">
                    <span
                        class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $product->stock }}
                    items
                </span>
            @endif
        </td>

        {{-- Actions --}}
        <td class="px-6 py-3 text-center">
            <div class="flex items-center justify-center gap-2">
                <a href="{{ route('admin.products.edit', $product) }}"
                    class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors"
                    title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                </a>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                    onsubmit="return confirm('Delete {{ addslashes($product->name) }}?');"
                    class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-colors"
                        title="Delete">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-14 text-center">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-40"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium">No products found</p>
                <button type="button" onclick="resetFilters()"
                    class="text-xs text-blue-600 font-semibold hover:underline">Clear all filters</button>
            </div>
        </td>
    </tr>
@endforelse
