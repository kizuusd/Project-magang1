<div x-data="{ open: {{ (old('_method') !== 'PUT' && $errors->any()) ? 'true' : 'false' }} }"
     @open-create-category-modal.window="open = true"
     x-show="open" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="open = false"></div>

    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        {{-- Modal Panel --}}
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full"
             @click.stop
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
            <div class="p-6 sm:p-8">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 leading-tight">Tambah Kategori</h2>
                        <p class="text-sm text-gray-500">Buat kategori baru untuk transaksi</p>
                    </div>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    <input type="hidden" name="_form_type" value="create">

                    <div class="mb-6">
                        <label for="create_category_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Kategori <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="create_category_name" value="{{ old('name') }}"
                               placeholder="Contoh: Makanan, Gaji, Transportasi"
                               class="block w-full rounded-xl border-gray-200 text-sm focus:border-[#386650] focus:ring-[#386650] bg-[#F4F3F0] text-gray-700 shadow-sm"
                               required>
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Tipe Kategori <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="income" class="peer sr-only"
                                       {{ old('type') === 'income' ? 'checked' : '' }}>
                                <div class="flex items-center justify-center p-3 border-2 border-gray-100 rounded-xl peer-checked:border-[#386650] peer-checked:bg-[#386650]/5 hover:bg-gray-50 transition duration-150">
                                    <span class="text-sm font-semibold text-gray-700 peer-checked:text-[#386650]">Pemasukan</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="expense" class="peer sr-only"
                                       {{ old('type', 'expense') === 'expense' ? 'checked' : '' }}>
                                <div class="flex items-center justify-center p-3 border-2 border-gray-100 rounded-xl peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 transition duration-150">
                                    <span class="text-sm font-semibold text-gray-700 peer-checked:text-red-600">Pengeluaran</span>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                        <button type="button" @click="open = false"
                                class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-xl font-semibold text-xs text-gray-700 hover:bg-gray-50 transition shadow-sm">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-[#386650] border border-transparent rounded-xl font-semibold text-xs text-white hover:bg-[#2d5241] transition shadow-sm">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
