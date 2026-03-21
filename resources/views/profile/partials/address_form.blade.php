<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 border-l-4 border-blue-600 pl-4">
            {{ __('Shipping Address') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 pl-5">
            {{ __('Manage your delivery locations and set your default shipping address.') }}
        </p>
    </header>

    <div x-data="{
        open: {{ isset($addresses) && count($addresses) > 0 && !$errors->any() ? 'false' : 'true' }},
        editing: false,
        formAction: '{{ route('addresses.store') }}',
        address: {
            full_name: '{{ old('full_name', auth()->user()->name) }}',
            phone_number: '{{ old('phone_number') }}',
            email: '{{ old('email', auth()->user()->email) }}',
            street_address: '{{ old('street_address') }}',
            city: '{{ old('city') }}',
            state: '{{ old('state') }}',
            postal_code: '{{ old('postal_code') }}',
            country: '{{ old('country', '') }}',
            address_type: '{{ old('address_type', 'Home') }}',
            is_default: {{ old('is_default') ? 'true' : 'false' }}
        },
        showDeleteModal: false,
        addressToDeleteUrl: '',
        confirmDelete(url) {
            this.addressToDeleteUrl = url;
            this.showDeleteModal = true;
        },
        editAddress(addr) {
            this.editing = true;
            this.open = true;
            this.formAction = '/addresses/' + addr.id;
            this.address = {
                full_name: addr.full_name,
                phone_number: addr.phone_number,
                email: addr.email || '',
                street_address: addr.street_address,
                city: addr.city,
                state: addr.state,
                postal_code: addr.postal_code,
                country: addr.country,
                address_type: addr.address_type,
                is_default: !!addr.is_default
            };
            this.$nextTick(() => {
                document.getElementById('full_name').focus();
            });
        },
        resetForm() {
            this.editing = false;
            if ({{ count($addresses) }} > 0) this.open = false;
            this.formAction = '{{ route('addresses.store') }}';
            this.address = {
                full_name: '{{ auth()->user()->name }}',
                phone_number: '',
                email: '{{ auth()->user()->email }}',
                street_address: '',
                city: '',
                state: '',
                postal_code: '',
                country: '',
                address_type: 'Home',
                is_default: false
            };
        }
    }">
        @if (isset($addresses) && count($addresses) > 0)
            <!-- Saved Addresses Gallery -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($addresses as $entry)
                    <div
                        class="relative p-5 rounded-2xl border {{ $entry->is_default ? 'border-blue-200 bg-blue-50/50' : 'border-gray-100 bg-gray-50/30' }} transition-all group hover:shadow-md">
                        @if ($entry->is_default)
                            <span
                                class="absolute -top-2.5 right-4 px-2 py-0.5 bg-blue-600 text-white text-[9px] font-bold rounded-full uppercase tracking-widest shadow-sm">Default</span>
                        @endif

                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-gray-900 text-sm">{{ $entry->full_name }}</h4>
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-bold uppercase transition-colors {{ $entry->address_type === 'Home' ? 'bg-orange-50 text-orange-600' : 'bg-purple-50 text-purple-600' }}">
                                        {{ $entry->address_type }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $entry->phone_number }}
                                </p>
                            </div>

                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                {{-- Edit Button --}}
                                <button type="button" @click="editAddress({{ json_encode($entry) }})"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 bg-white shadow-sm rounded-lg border border-gray-100 transition-all hover:scale-110"
                                    title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                @if (!$entry->is_default)
                                    <form action="{{ route('addresses.default', $entry) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-emerald-600 bg-white shadow-sm rounded-lg border border-gray-100 transition-all hover:scale-110"
                                            title="Set Default">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete Button (Custom Alert) --}}
                                <button type="button" @click="confirmDelete('{{ route('addresses.destroy', $entry) }}')"
                                    class="p-1.5 text-gray-400 hover:text-red-500 bg-white shadow-sm rounded-lg border border-gray-100 transition-all hover:scale-110"
                                    title="Delete">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="text-xs text-gray-600 space-y-0.5">
                            <p>{{ $entry->street_address }}</p>
                            <p>{{ $entry->city }}, {{ $entry->state }} {{ $entry->postal_code }}</p>
                            <p>{{ $entry->country }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-gray-100 my-10" x-show="!editing"></div>
        @endif

        {{-- Hidden Delete Form --}}
        <form x-ref="deleteForm" :action="addressToDeleteUrl" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        {{-- Premium Delete Confirmation Modal --}}
        <div x-show="showDeleteModal" 
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="showDeleteModal = false"
            style="display: none;">
            
            <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden"
                @click.away="showDeleteModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="scale-90"
                x-transition:enter-end="scale-100">
                
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Address?</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-8">
                        Are you sure you want to remove this shipping address? This action cannot be undone.
                    </p>
                    
                    <div class="flex flex-col gap-3">
                        <button type="button" @click="$refs.deleteForm.submit()"
                            class="w-full py-4 bg-rose-500 text-white font-bold rounded-2xl hover:bg-rose-600 transition-all shadow-lg shadow-rose-200">
                            Yes, Delete This Address
                        </button>
                        <button type="button" @click="showDeleteModal = false"
                            class="w-full py-4 text-gray-400 font-bold hover:text-gray-600 transition-colors">
                            No, Keep It
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6">
            @if (count($addresses) > 0)
                <button @click="editing ? resetForm() : (open = !open)"
                    class="flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors mb-6 group">
                    <div
                        class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-45' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span x-text="editing ? 'Cancel Editing' : 'Add New Delivery Address'"></span>
                </button>
            @endif

            <form method="post" :action="formAction" class="space-y-6" x-show="open" x-collapse x-transition>
                @csrf
                <template x-if="editing">
                    @method('PATCH')
                </template>

                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest pt-2"
                    x-text="editing ? 'Edit Address' : 'Add New Address'">
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <!-- Full Name -->
                    <div>
                        <x-input-label for="full_name" :value="__('Full Name')" />
                        <x-text-input id="full_name" name="full_name" type="text" class="mt-1 block w-full"
                            x-model="address.full_name" required placeholder="e.g. John Doe" />
                        <x-input-error class="mt-2" :messages="$errors->get('full_name')" />
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <x-input-label for="phone_number" :value="__('Phone Number')" />
                        <x-text-input id="phone_number" name="phone_number" type="tel" class="mt-1 block w-full"
                            x-model="address.phone_number" required placeholder="+1 234 567 890" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                    </div>
                </div>

                <!-- Email Address (Optional) -->
                <div>
                    <x-input-label for="email" :value="__('Email Address (Optional)')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                        x-model="address.email" placeholder="john@example.com" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <!-- Street Address -->
                <div>
                    <x-input-label for="street_address" :value="__('Street Address')" />
                    <x-text-input id="street_address" name="street_address" type="text" class="mt-1 block w-full"
                        x-model="address.street_address" required placeholder="St name, House/Apartment No" />
                    <x-input-error class="mt-2" :messages="$errors->get('street_address')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- City -->
                    <div>
                        <x-input-label for="city" :value="__('City')" />
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                            x-model="address.city" required placeholder="City" />
                        <x-input-error class="mt-2" :messages="$errors->get('city')" />
                    </div>

                    <!-- State -->
                    <div>
                        <x-input-label for="state" :value="__('State / Province')" />
                        <x-text-input id="state" name="state" type="text" class="mt-1 block w-full"
                            x-model="address.state" required placeholder="State" />
                        <x-input-error class="mt-2" :messages="$errors->get('state')" />
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <x-input-label for="postal_code" :value="__('Postal Code')" />
                        <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full"
                            x-model="address.postal_code" required placeholder="Zip" />
                        <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                    </div>
                </div>

                <!-- Country Selection -->
                <div>
                    <x-input-label for="country" :value="__('Country')" />
                    <select name="country" id="country" x-model="address.country"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-200">
                        <option value="" disabled selected>{{ __('Select a country') }}</option>
                        @foreach (['United States', 'United Kingdom', 'Canada', 'Australia', 'Germany', 'France', 'Japan', 'Cambodia'] as $country)
                            <option value="{{ $country }}">{{ $country }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('country')" />
                </div>

                <div class="flex flex-col md:flex-row gap-8 py-4 border-y border-gray-50">
                    <!-- Address Type -->
                    <div class="flex-1">
                        <x-input-label :value="__('Address Type')" />
                        <div class="flex gap-4 mt-2">
                            <label class="relative flex-1 cursor-pointer">
                                <input type="radio" name="address_type" value="Home" class="sr-only peer"
                                    x-model="address.address_type">
                                <div
                                    class="w-full py-2.5 text-center border border-gray-100 rounded-xl peer-checked:border-blue-600 peer-checked:bg-blue-50/50 transition-all hover:bg-gray-50">
                                    <span
                                        class="text-xs font-bold text-gray-500 peer-checked:text-blue-600 uppercase tracking-widest">Home</span>
                                </div>
                            </label>
                            <label class="relative flex-1 cursor-pointer">
                                <input type="radio" name="address_type" value="Office" class="sr-only peer"
                                    x-model="address.address_type">
                                <div
                                    class="w-full py-2.5 text-center border border-gray-100 rounded-xl peer-checked:border-blue-600 peer-checked:bg-blue-50/50 transition-all hover:bg-gray-50">
                                    <span
                                        class="text-xs font-bold text-gray-500 peer-checked:text-blue-600 uppercase tracking-widest">Office</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Default Checkbox -->
                    <div class="flex items-end flex-1 pb-1">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="is_default" value="1" x-model="address.is_default"
                                class="rounded-lg border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 w-5 h-5 transition-all">
                            <input type="hidden" name="is_default" value="0" x-show="!address.is_default"
                                disabled>
                            <span
                                class="ml-3 text-sm font-medium text-gray-600 group-hover:text-blue-600 transition-colors">
                                {{ __('Set as default shipping address') }}
                            </span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <x-primary-button
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 transition-all shadow-md shadow-blue-600/10">
                        <span x-text="editing ? 'Update Address' : 'Save Address'"></span>
                    </x-primary-button>

                    <button type="button" x-show="editing" @click="resetForm()"
                        class="text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors">
                        Cancel
                    </button>

                    @if (session('success'))
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-gray-600">
                            {{ __('Saved.') }}
                        </p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>
