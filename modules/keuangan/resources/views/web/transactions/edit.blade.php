<div x-data="{ open: {{ (old('_method') === 'PUT' && old('transaction_id') == $transaction->id && $errors->any()) ? 'true' : 'false' }} }"
     @open-edit-modal-{{ $transaction->id }}.window="open = true"
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
             
            <div class="p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 leading-tight">Edit Transaksi</h2>
                        <p class="text-sm text-gray-500">Ubah rincian transaksi Anda</p>
                    </div>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('transactions.update', $transaction) }}" id="transaction-edit-form-{{ $transaction->id }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

                    {{-- Tipe Transaksi --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Transaksi <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="income" class="peer sr-only" onchange="filterCategoriesEdit{{ $transaction->id }}()"
                                       {{ old('type', $transaction->type) === 'income' ? 'checked' : '' }}>
                                <div class="flex items-center justify-center p-3 border-2 border-gray-100 rounded-xl
                                            peer-checked:border-[#386650] peer-checked:bg-[#386650]/5
                                            hover:bg-gray-50 transition duration-150">
                                    <span class="text-sm font-semibold text-gray-700 peer-checked:text-[#386650]">Pemasukan</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="expense" class="peer sr-only" onchange="filterCategoriesEdit{{ $transaction->id }}()"
                                       {{ old('type', $transaction->type) === 'expense' ? 'checked' : '' }}>
                                <div class="flex items-center justify-center p-3 border-2 border-gray-100 rounded-xl
                                            peer-checked:border-red-500 peer-checked:bg-red-50
                                            hover:bg-gray-50 transition duration-150">
                                    <span class="text-sm font-semibold text-gray-700 peer-checked:text-red-600">Pengeluaran</span>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori & Tanggal --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label for="edit_category_id_{{ $transaction->id }}" class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                            <select name="category_id" id="edit_category_id_{{ $transaction->id }}"
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#386650] focus:ring-[#386650] sm:text-sm bg-[#F4F3F0]">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" data-type="{{ $cat->type }}"
                                            {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="edit_transaction_date_{{ $transaction->id }}" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="transaction_date" id="edit_transaction_date_{{ $transaction->id }}"
                                   value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}"
                                   class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#386650] focus:ring-[#386650] sm:text-sm bg-[#F4F3F0]" required>
                            @error('transaction_date')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Jumlah --}}
                    <div class="mb-5">
                        <label for="edit_amount_{{ $transaction->id }}" class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-500 text-sm font-medium pointer-events-none">Rp</span>
                            <input type="number" name="amount" id="edit_amount_{{ $transaction->id }}"
                                   value="{{ old('amount', (int) $transaction->amount) }}" placeholder="0" min="1"
                                   class="block w-full pl-10 rounded-xl border-gray-200 shadow-sm focus:border-[#386650] focus:ring-[#386650] sm:text-sm bg-[#F4F3F0]" required>
                        </div>
                        @error('amount')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-6">
                        <label for="edit_description_{{ $transaction->id }}" class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                        <input type="text" name="description" id="edit_description_{{ $transaction->id }}"
                               value="{{ old('description', $transaction->description) }}" placeholder="Contoh: Gaji Mei 2025"
                               class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#386650] focus:ring-[#386650] sm:text-sm bg-[#F4F3F0]">
                        @error('description')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tombol --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="open = false"
                                class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-xl font-semibold text-xs text-gray-700 hover:bg-gray-50 transition shadow-sm">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-[#386650] border border-transparent rounded-xl font-semibold text-xs text-white hover:bg-[#2d5241] transition shadow-sm">
                            Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function filterCategoriesEdit{{ $transaction->id }}() {
        const modal = document.getElementById('transaction-edit-form-{{ $transaction->id }}');
        if (!modal) return;
        const selectedType = modal.querySelector('input[name="type"]:checked')?.value;
        const categorySelect = document.getElementById('edit_category_id_{{ $transaction->id }}');
        const options = categorySelect.querySelectorAll('option[data-type]');

        options.forEach(option => {
            if (!selectedType || option.dataset.type === selectedType) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
                if (option.selected) {
                    categorySelect.value = '';
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        filterCategoriesEdit{{ $transaction->id }}();
    });
</script>
