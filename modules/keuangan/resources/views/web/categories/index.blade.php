<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between" x-data="{}">
            <h2 class="font-bold text-xl text-gray-900 leading-tight">
                {{ __('Kategori') }}
            </h2>
            <button @click="$dispatch('open-create-category-modal')"
               class="inline-flex items-center px-4 py-2 bg-[#386650] border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#2d5241] focus:outline-none focus:ring-2 focus:ring-[#386650]/40 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kategori
            </button>
        </div>
    </x-slot>

    <style>
        /* Sembunyikan top navigation bar bawaan layout app */
        nav[x-data] { display: none !important; }
        
        /* Buat header menyatu dengan background halaman */
        header.bg-white.shadow {
            background-color: var(--app-bg) !important;
            box-shadow: none !important;
        }
        
        @media (min-width: 1024px) {
            header.bg-white.shadow { padding-left: 16rem !important; }
        }
        
        * { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #C8C5BE; border-radius: 99px; }
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <x-sidebar />

    <div class="py-8 lg:ml-64" style="background-color: var(--app-bg); min-height: calc(100vh - 64px);">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{}">

            @if (session('success'))
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 p-4 shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Income Categories --}}
                <div class="bg-white rounded-2xl shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center mb-6 pb-4 border-b border-gray-100">
                            <div class="flex-shrink-0 bg-emerald-50 rounded-xl p-2 mr-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Pemasukan</h3>
                            <span class="ml-auto bg-emerald-50 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-md">
                                {{ $categories->where('type', 'income')->count() }} kategori
                            </span>
                        </div>
                        <div class="space-y-3">
                            @forelse ($categories->where('type', 'income') as $category)
                                <div class="flex items-center justify-between p-3.5 bg-white border border-gray-100 rounded-xl hover:bg-[#F4F3F0] transition duration-150">
                                    <div class="flex items-center min-w-0">
                                        <span class="text-sm font-semibold text-gray-800 truncate">{{ $category->name }}</span>
                                        @if ($category->transactions_count > 0)
                                            <span class="ml-2 text-xs font-medium text-gray-500 flex-shrink-0">{{ $category->transactions_count }} transaksi</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2 ml-2 flex-shrink-0">
                                        {{-- Shortcut tambah transaksi --}}
                                        <a href="{{ route('transactions.create', ['type' => 'income', 'category_id' => $category->id]) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-[#386650] bg-[#386650]/10 rounded-lg hover:bg-[#386650]/20 transition duration-150"
                                           title="Tambah transaksi kategori ini">
                                            + Transaksi
                                        </a>
                                        <button type="button" @click="$dispatch('open-edit-category-modal-{{ $category->id }}')"
                                           class="inline-flex items-center p-1.5 text-gray-400 hover:text-[#386650] hover:bg-white rounded-lg transition duration-150" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center p-1.5 text-gray-400 hover:text-red-600 hover:bg-white rounded-lg transition duration-150" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <p class="text-sm text-gray-500">Belum ada kategori pemasukan</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Expense Categories --}}
                <div class="bg-white rounded-2xl shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center mb-6 pb-4 border-b border-gray-100">
                            <div class="flex-shrink-0 bg-red-50 rounded-xl p-2 mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Pengeluaran</h3>
                            <span class="ml-auto bg-red-50 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-md">
                                {{ $categories->where('type', 'expense')->count() }} kategori
                            </span>
                        </div>
                        <div class="space-y-3">
                            @forelse ($categories->where('type', 'expense') as $category)
                                <div class="flex items-center justify-between p-3.5 bg-white border border-gray-100 rounded-xl hover:bg-[#F4F3F0] transition duration-150">
                                    <div class="flex items-center min-w-0">
                                        <span class="text-sm font-semibold text-gray-800 truncate">{{ $category->name }}</span>
                                        @if ($category->transactions_count > 0)
                                            <span class="ml-2 text-xs font-medium text-gray-500 flex-shrink-0">{{ $category->transactions_count }} transaksi</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2 ml-2 flex-shrink-0">
                                        {{-- Shortcut tambah transaksi --}}
                                        <a href="{{ route('transactions.create', ['type' => 'expense', 'category_id' => $category->id]) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-[#386650] bg-[#386650]/10 rounded-lg hover:bg-[#386650]/20 transition duration-150"
                                           title="Tambah transaksi kategori ini">
                                            + Transaksi
                                        </a>
                                        <button type="button" @click="$dispatch('open-edit-category-modal-{{ $category->id }}')"
                                           class="inline-flex items-center p-1.5 text-gray-400 hover:text-[#386650] hover:bg-white rounded-lg transition duration-150" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center p-1.5 text-gray-400 hover:text-red-600 hover:bg-white rounded-lg transition duration-150" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <p class="text-sm text-gray-500">Belum ada kategori pengeluaran</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- Include Modals --}}
    @include('keuangan::web.categories.create')

    @foreach ($categories as $category)
        @include('keuangan::web.categories.edit', ['category' => $category])
    @endforeach

</x-app-layout>
