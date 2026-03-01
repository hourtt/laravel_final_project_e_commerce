<x-admin-layout>
    <div class="max-w-3xl bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path
                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-gray-900">Edit Product</h2>
                <p class="text-xs text-gray-400">Editing: <span
                        class="font-semibold text-gray-600">{{ $product->name }}</span></p>
            </div>
        </div>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
            class="p-6">
            @csrf
            @method('PUT')

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
                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Price ($)</label>
                    <div class="relative">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-semibold text-sm">$</span>
                        <input type="number" step="0.01" min="0" name="price"
                            value="{{ old('price', $product->price) }}"
                            class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm pl-7"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Stock Quantity</label>
                    <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock) }}"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                        required>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                    <select name="category_id"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                        required>
                        <option value="">Select a category…</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} {{ $category->icon }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm" required>{{ old('description', $product->description) }}</textarea>
                </div>

                {{-- ===== IMAGE UPLOAD ===== --}}
                <div class="sm:col-span-2" id="edit-uploader">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Product Image</label>

                    {{-- Picker area --}}
                    <div id="edit-picker"
                        class="{{ $product->product_image ? 'hidden' : '' }} flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 py-10 px-6 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 transition-all duration-200"
                        onclick="document.getElementById('edit-file-input').click()"
                        ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-50')"
                        ondragleave="this.classList.remove('border-blue-400','bg-blue-50')"
                        ondrop="event.preventDefault(); this.classList.remove('border-blue-400','bg-blue-50'); editUploader.dropFile(event)">
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Click or drag & drop to upload</p>
                            <p class="text-xs text-gray-400 mt-0.5">PNG, JPG, GIF, WebP · Max 4 MB</p>
                        </div>
                    </div>

                    {{-- Preview grid --}}
                    <div id="edit-preview-area" class="{{ $product->product_image ? '' : 'hidden' }}">
                        {{-- Status bar --}}
                        <div class="flex items-center justify-between mb-3">
                            <p id="edit-status-text" class="text-xs font-semibold text-gray-500">
                                {{ $product->product_image ? '1 file selected' : '' }}
                            </p>
                            <button type="button" onclick="document.getElementById('edit-file-input').click()"
                                class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors">
                                Change image
                            </button>
                        </div>

                        {{-- Thumbnail card grid --}}
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200">
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                <div class="relative group">
                                    {{-- X button —— always shown --}}
                                    <button type="button" onclick="editUploader.remove()"
                                        class="absolute -top-2 -left-2 z-10 w-6 h-6 rounded-full bg-white border border-gray-300 shadow flex items-center justify-center text-gray-600 hover:bg-red-50 hover:border-red-300 hover:text-red-500 transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    {{-- Saved badge (shown only for existing server image) --}}
                                    <span id="edit-saved-badge"
                                        class="{{ $product->product_image ? '' : 'hidden' }} absolute -bottom-2 left-1/2 -translate-x-1/2 z-10 px-1.5 py-0.5 rounded-full bg-emerald-500 text-white text-[9px] font-bold shadow whitespace-nowrap">
                                        Saved
                                    </span>
                                    {{-- New badge (shown only after a new file is picked) --}}
                                    <span id="edit-new-badge"
                                        class="hidden absolute -bottom-2 left-1/2 -translate-x-1/2 z-10 px-1.5 py-0.5 rounded-full bg-blue-500 text-white text-[9px] font-bold shadow whitespace-nowrap">
                                        New
                                    </span>
                                    {{-- Thumbnail --}}
                                    <img id="edit-thumb-img" src="{{ $product->image_url }}" alt="Product Image"
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
                                    <p id="edit-file-name"
                                        class="text-xs font-semibold text-gray-700 truncate max-w-xs">
                                        {{ $product->product_image ? basename($product->product_image) : '' }}
                                    </p>
                                    <p id="edit-file-size" class="text-[10px] text-gray-400">
                                        {{ $product->product_image ? 'Saved on server' : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Error --}}
                    <p id="edit-error" class="hidden text-xs text-red-500 mt-1.5 font-medium"></p>

                    <input type="file" id="edit-file-input" name="product_image" accept="image/*"
                        class="hidden">
                    <input type="hidden" id="edit-remove-flag" name="remove_image" value="0">
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-100 pt-6">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 text-white font-bold text-sm hover:bg-amber-600 transition shadow-sm shadow-amber-400/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Update Product
                </button>
                <a href="{{ route('admin.products.index') }}"
                    class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 font-bold text-sm hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        const editUploader = (() => {
            const picker = document.getElementById('edit-picker');
            const previewArea = document.getElementById('edit-preview-area');
            const thumbImg = document.getElementById('edit-thumb-img');
            const fileNameEl = document.getElementById('edit-file-name');
            const fileSizeEl = document.getElementById('edit-file-size');
            const errorEl = document.getElementById('edit-error');
            const fileInput = document.getElementById('edit-file-input');
            const removeFlag = document.getElementById('edit-remove-flag');
            const savedBadge = document.getElementById('edit-saved-badge');
            const newBadge = document.getElementById('edit-new-badge');
            const statusText = document.getElementById('edit-status-text');

            function showPreview(src, name, size, isNew) {
                thumbImg.src = src;
                fileNameEl.textContent = name;
                fileSizeEl.textContent = size;
                statusText.textContent = '1 file selected';
                if (isNew) {
                    savedBadge.classList.add('hidden');
                    newBadge.classList.remove('hidden');
                } else {
                    newBadge.classList.add('hidden');
                    savedBadge.classList.remove('hidden');
                }
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
                removeFlag.value = '0';
                const reader = new FileReader();
                reader.onload = e => showPreview(e.target.result, file.name, (file.size / 1024).toFixed(1) + ' KB',
                    true);
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
                removeFlag.value = '1';
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
