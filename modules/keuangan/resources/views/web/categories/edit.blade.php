<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-gray-600 mr-3 transition duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-bold text-xl text-gray-900 leading-tight">
                {{ __('Edit Kategori') }}
            </h2>
        </div>
    </x-slot>

    <style>
        /* Sembunyikan top navigation bar bawaan layout app */
        nav[x-data] { display: none !important; }
        
        /* Buat header menyatu dengan background halaman */
        header.bg-white.shadow {
            background-color: #F4F3F0 !important;
            box-shadow: none !important;
        }
        
        * { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #C8C5BE; border-radius: 99px; }
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <div class="py-8" style="background-color: #F4F3F0; min-height: calc(100vh - 64px);">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('categories.update', $category) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Kategori <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                                   class="block w-full rounded-xl border-gray-200 text-sm focus:border-[#386650] focus:ring-[#386650] bg-[#F4F3F0] text-gray-700 shadow-sm @error('name') border-red-300 @enderror"
                                   required autofocus>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Tipe Kategori <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="type" value="income" class="peer sr-only"
                                           {{ old('type', $category->type) === 'income' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center p-4 border border-gray-200 rounded-xl peer-checked:border-[#386650] peer-checked:bg-[#386650]/5 hover:bg-gray-50 transition duration-150">
                                        <svg class="w-5 h-5 text-gray-400 peer-checked:text-[#386650] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-500 peer-checked:text-[#386650] peer-checked:font-semibold">Pemasukan</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="type" value="expense" class="peer sr-only"
                                           {{ old('type', $category->type) === 'expense' ? 'checked' : '' }}>
                                    <div class="flex items-center justify-center p-4 border border-gray-200 rounded-xl peer-checked:border-[#386650] peer-checked:bg-[#386650]/5 hover:bg-gray-50 transition duration-150">
                                        <svg class="w-5 h-5 text-gray-400 peer-checked:text-[#386650] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-500 peer-checked:text-[#386650] peer-checked:font-semibold">Pengeluaran</span>
                                    </div>
                                </label>
                            </div>
                            @error('type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                            <a href="{{ route('categories.index') }}"
                               class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 rounded-xl font-semibold text-xs text-gray-600 uppercase tracking-widest hover:bg-[#F4F3F0] transition duration-150">
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 bg-[#386650] border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#2d5241] focus:outline-none focus:ring-2 focus:ring-[#386650]/40 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
