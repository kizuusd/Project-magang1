<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 mr-3 transition duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Transaksi
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 overflow-hidden sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('transactions.update', $transaction) }}" id="transaction-edit-form">
                        @csrf
                        @method('PUT')

                        {{-- Tipe Transaksi --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Tipe Transaksi <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="type" value="income" id="type-income" class="peer sr-only"
                                           {{ old('type', $transaction->type) === 'income' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg
                                                peer-checked:border-emerald-500 peer-checked:bg-emerald-50
                                                hover:bg-gray-50 transition duration-150">
                                        <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Pemasukan</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="type" value="expense" id="type-expense" class="peer sr-only"
                                           {{ old('type', $transaction->type) === 'expense' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg
                                                peer-checked:border-rose-500 peer-checked:bg-rose-50
                                                hover:bg-gray-50 transition duration-150">
                                        <svg class="w-5 h-5 text-rose-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Pengeluaran</span>
                                    </div>
                                </label>
                            </div>
                            @error('type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="mb-6">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Kategori
                            </label>
                            <select name="category_id" id="category_id"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm @error('category_id') border-red-300 @enderror">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                            data-type="{{ $cat->type }}"
                                            {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jumlah --}}
                        <div class="mb-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm font-medium pointer-events-none">
                                    Rp
                                </span>
                                <input type="number" name="amount" id="amount"
                                       value="{{ old('amount', (int) $transaction->amount) }}"
                                       placeholder="0"
                                       min="1"
                                       class="block w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm @error('amount') border-red-300 @enderror"
                                       required>
                            </div>
                            @error('amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi
                            </label>
                            <input type="text" name="description" id="description"
                                   value="{{ old('description', $transaction->description) }}"
                                   placeholder="Contoh: Gaji Mei 2025, Belanja Supermarket..."
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm @error('description') border-red-300 @enderror">
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal --}}
                        <div class="mb-6">
                            <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="transaction_date" id="transaction_date"
                                   value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm @error('transaction_date') border-red-300 @enderror"
                                   required>
                            @error('transaction_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol --}}
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                                Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filter opsi kategori sesuai tipe yang dipilih
        function filterCategories() {
            const selectedType = document.querySelector('input[name="type"]:checked')?.value;
            const categorySelect = document.getElementById('category_id');
            const currentValue = categorySelect.value;
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
            filterCategories();
            document.querySelectorAll('input[name="type"]').forEach(radio => {
                radio.addEventListener('change', filterCategories);
            });
        });
    </script>
</x-app-layout>
