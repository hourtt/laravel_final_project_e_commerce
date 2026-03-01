<x-admin-layout>
    <div class="max-w-3xl bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-gray-900">Add Product</h2>
                <p class="text-xs text-gray-400">Fill in the details below to create a new product.</p>
            </div>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-100">
                    <p class="text-sm font-semibold text-red-600 mb-1">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li class="text-xs text-red-500">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Product Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                        placeholder="e.g. iPhone 15 Pro Max" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Price ($)</label>
                    <div class="relative">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-semibold text-sm">$</span>
                        <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}"
                            class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm pl-7"
                            placeholder="0.00" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Stock Quantity</label>
                    <input type="number" min="0" name="stock" value="{{ old('stock') }}"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                        placeholder="0" required>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                    <select name="category_id"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                        required>
                        <option value="">Select a category…</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} {{ $category->icon }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                        placeholder="Describe the product…" required>{{ old('description') }}</textarea>
                </div>

                {{-- ===== IMAGE UPLOAD ===== --}}
                <div class="sm:col-span-2" id="create-uploader">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Product Image</label>

                    {{-- Picker area (shown when no image chosen) --}}
                    <div id="create-picker"
                        class="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 py-10 px-6 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 transition-all duration-200"
                        onclick="document.getElementById('create-file-input').click()"
                        ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-50')"
                        ondragleave="this.classList.remove('border-blue-400','bg-blue-50')"
                        ondrop="event.preventDefault(); this.classList.remove('border-blue-400','bg-blue-50'); createUploader.dropFile(event)">
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Click or drag & drop to upload</p>
                            <p class="text-xs text-gray-400 mt-0.5">PNG, JPG, GIF, WebP · Max 4 MB · Leave blank for
                                category icon</p>
                        </div>
                    </div>

                    {{-- Preview grid (shown after image chosen) --}}
                    <div id="create-preview-area" class="hidden">
                        {{-- Status bar --}}
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-gray-500">1 file selected</p>
                            <button type="button" onclick="document.getElementById('create-file-input').click()"
                                class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors">
                                Change image
                            </button>
                        </div>

                        {{-- Thumbnail card --}}
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200">
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                <div class="relative group" id="create-thumb-card">
                                    {{-- X button --}}
                                    <button type="button" onclick="createUploader.remove()"
                                        class="absolute -top-2 -left-2 z-10 w-6 h-6 rounded-full bg-white border border-gray-300 shadow flex items-center justify-center text-gray-600 hover:bg-red-50 hover:border-red-300 hover:text-red-500 transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    {{-- Thumbnail image --}}
                                    <img id="create-thumb-img" src="" alt="Preview"
                                        class="w-full aspect-square object-cover rounded-xl border border-gray-200 shadow-sm">
                                </div>
                            </div>

                            {{-- File info --}}
                            <div class="mt-3 flex items-center gap-2">
                                <div
                                    class="w-6 h-6 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-blue-500"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p id="create-file-name"
                                        class="text-xs font-semibold text-gray-700 truncate max-w-xs"></p>
                                    <p id="create-file-size" class="text-[10px] text-gray-400"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Error --}}
                    <p id="create-error" class="hidden text-xs text-red-500 mt-1.5 font-medium"></p>

                    <input type="file" id="create-file-input" name="product_image" accept="image/*"
                        class="hidden">
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-100 pt-6">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition shadow-sm shadow-blue-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Save Product
                </button>
                <a href="{{ route('admin.products.index') }}"
                    class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 font-bold text-sm hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        const createUploader = (() => {
            const picker = document.getElementById('create-picker');
            const previewArea = document.getElementById('create-preview-area');
            const thumbImg = document.getElementById('create-thumb-img');
            const fileNameEl = document.getElementById('create-file-name');
            const fileSizeEl = document.getElementById('create-file-size');
            const errorEl = document.getElementById('create-error');
            const fileInput = document.getElementById('create-file-input');

            function showPreview(src, name, size) {
                thumbImg.src = src;
                fileNameEl.textContent = name;
                fileSizeEl.textContent = size;
                picker.classList.add('hidden');
                previewArea.classList.remove('hidden');
                errorEl.classList.add('hidden');
            }

            function showPicker() {
                previewArea.classList.add('hidden');
                picker.classList.remove('hidden');
                thumbImg.src = '';
                fileInput.value = '';
            }

            function handleFile(file) {
                errorEl.classList.add('hidden');
                if (!file) return;
                const allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowed.includes(file.type)) {
                    errorEl.textContent = 'Invalid type. Please upload JPG, PNG, GIF, or WebP.';
                    errorEl.classList.remove('hidden');
                    fileInput.value = '';
                    return;
                }
                if (file.size > 4 * 1024 * 1024) {
                    errorEl.textContent = 'File too large. Max 4 MB.';
                    errorEl.classList.remove('hidden');
                    fileInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = e => showPreview(e.target.result, file.name, (file.size / 1024).toFixed(1) + ' KB');
                reader.readAsDataURL(file);
            }

            function dropFile(event) {
                const file = event.dataTransfer.files[0];
                if (!file) return;
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                handleFile(file);
            }

            function remove() {
                showPicker();
            }

            fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

            return {
                remove,
                dropFile
            };
        })();
    </script>
</x-admin-layout>
