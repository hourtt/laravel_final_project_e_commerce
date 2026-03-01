<x-admin-layout>
    <div class="max-w-2xl bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">Add Category</h2>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6">
            @csrf

            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                    <input type="text" name="name"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Icon Emoji</label>
                    <input type="text" name="icon"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                        placeholder="📱">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition">Save
                    Category</button>
                <a href="{{ route('admin.categories.index') }}"
                    class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 font-bold text-sm hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>