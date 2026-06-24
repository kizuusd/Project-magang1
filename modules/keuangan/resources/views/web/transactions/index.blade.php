<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                {{-- Sunburst logo --}}
                <svg class="w-11 h-11 text-[#386650]" viewBox="0 0 44 44" fill="none">
                    <circle cx="22" cy="22" r="4.5" fill="currentColor" opacity=".75"/>
                    @foreach([0, 40, 80, 120, 160, 200, 240, 280, 320] as $deg)
                        <line x1="22" y1="22"
                              x2="{{ 22 + 10*cos(deg2rad($deg)) }}"
                              y2="{{ 22 + 10*sin(deg2rad($deg)) }}"
                              stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity=".4"/>
                    @endforeach
                </svg>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 leading-tight">Hello, {{ Auth::user()->name ?? 'User' }}!</h1>
                    <p class="text-sm text-gray-400">Explore information and activity about your property</p>
                </div>
            </div>
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

        /* ─── Pagination Override ─── */
        nav[aria-label="Pagination"] a,
        nav[role="navigation"] a,
        nav[aria-label="Pagination"] span:not([aria-current="page"]),
        nav[role="navigation"] span:not([aria-current="page"]) {
            background-color: var(--card-bg) !important;
            color: #386650 !important;
            border-color: var(--border-color-2) !important;
        }
        
        nav[aria-label="Pagination"] span[aria-current="page"] > span,
        nav[role="navigation"] span[aria-current="page"] > span {
            background-color: #386650 !important;
            border-color: #386650 !important;
            color: #fff !important;
        }
        
        nav[aria-label="Pagination"] a:hover,
        nav[role="navigation"] a:hover {
            background-color: #386650 !important;
            border-color: #386650 !important;
            color: #fff !important;
        }
        
        /* Disabled prev/next text color */
        nav[aria-label="Pagination"] span[aria-disabled] span,
        nav[role="navigation"] span[aria-disabled] span,
        nav[aria-label="Pagination"] span[aria-disabled],
        nav[role="navigation"] span[aria-disabled] {
            color: #9ca3af !important;
            background-color: var(--card-bg) !important;
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="mb-5 flex items-center gap-2 bg-[#386650]/10 border border-[#386650]/20 text-[#386650] text-sm font-medium px-4 py-2.5 rounded-xl">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                {{-- 1. Total Transaksi --}}
                <div class="card bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#F4F3F0] flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 mb-0.5">Total Transaksi</p>
                                <p class="text-[1.6rem] font-bold text-gray-900 leading-none">{{ number_format($totalTransactions) }}</p>
                            </div>
                        </div>
                        <svg class="w-14 h-9 text-[#6a9e78] shrink-0" viewBox="0 0 56 36" fill="none">
                            <polyline points="2,32 10,24 20,28 30,14 40,18 48,8 54,12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>

                {{-- 2. Total Pemasukan --}}
                <div class="card bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#F4F3F0] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-[#386650]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v10M9 9.5c0-1.38 1.343-2.5 3-2.5s3 1.12 3 2.5-1.343 2.5-3 2.5-3 1.12-3 2.5 1.343 2.5 3 2.5 3-1.12 3-2.5"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 mb-0.5">Total Pemasukan</p>
                            <p class="text-[1.6rem] font-bold text-gray-900 leading-none">{{ format_rupiah($allTimeIncome) }}</p>
                        </div>
                    </div>
                </div>

                {{-- 3. Total Pengeluaran --}}
                <div class="card bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#F4F3F0] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 mb-0.5">Total Pengeluaran</p>
                            <p class="text-[1.6rem] font-bold text-gray-900 leading-none">{{ format_rupiah($allTimeExpense) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter & Actions --}}
            <div class="bg-white rounded-2xl shadow-sm p-5 mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
                <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label for="filter-type" class="block text-xs font-medium text-gray-500 mb-1.5">Tipe</label>
                        <select id="filter-type" name="type"
                                class="rounded-xl border-gray-200 text-sm focus:border-[#386650] focus:ring-[#386650] bg-[#F4F3F0] text-gray-700">
                            <option value="">Semua Tipe</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <label for="filter-category" class="block text-xs font-medium text-gray-500 mb-1.5">Kategori</label>
                        <select id="filter-category" name="category_id"
                                class="rounded-xl border-gray-200 text-sm focus:border-[#386650] focus:ring-[#386650] bg-[#F4F3F0] text-gray-700">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-[#386650] border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#2d5241] transition duration-150 shadow-sm">
                            Filter
                        </button>
                        @if (request()->hasAny(['type', 'category_id']))
                            <a href="{{ route('transactions.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl font-semibold text-xs text-gray-600 uppercase tracking-widest hover:bg-[#F4F3F0] transition duration-150">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <div class="flex items-center gap-3">
                    {{-- Action: Tambah Transaksi --}}
                    <div class="shrink-0" x-data="{}">
                        <button @click="$dispatch('open-create-modal')"
                           class="inline-flex items-center justify-center w-[38px] h-[38px] bg-[#386650] border border-transparent rounded-xl text-white hover:bg-[#2d5241] focus:outline-none focus:ring-2 focus:ring-[#386650]/40 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm"
                           title="Tambah Transaksi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tabel Transaksi --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" x-data="{}">
                @if ($transactions->isEmpty())
                    <div class="text-center py-16">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-[#F4F3F0] flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-600 text-sm font-semibold">Belum ada transaksi</p>
                        <p class="text-gray-400 text-xs mt-1">Mulai tambahkan pemasukan atau pengeluaran Anda</p>
                        <div class="mt-5 flex justify-center gap-2">
                            <button @click="$dispatch('open-create-modal')"
                               class="inline-flex items-center px-5 py-2.5 bg-[#386650] border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#2d5241] transition shadow-sm">
                                + Tambah Transaksi
                            </button>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr style="background-color: #F4F3F0;">
                                    <th class="px-6 py-3.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-3.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3.5 text-right text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3.5 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @foreach ($transactions as $transaction)
                                    <tr class="hover:bg-[#F4F3F0]/50 transition duration-100">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $transaction->transaction_date->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                                            {{ $transaction->category?->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                            {{ $transaction->description ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($transaction->type === 'income')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-semibold bg-[#386650]/10 text-[#386650]">
                                                    Pemasukan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-semibold bg-red-50 text-red-500">
                                                    Pengeluaran
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right {{ $transaction->type === 'income' ? 'text-[#386650]' : 'text-red-500' }}">
                                            {{ $transaction->type === 'income' ? '+' : '-' }} {{ format_rupiah($transaction->amount) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-1">
                                                <button type="button" @click="$dispatch('open-edit-modal-{{ $transaction->id }}')"
                                                   class="inline-flex items-center p-1.5 text-gray-400 hover:text-[#386650] hover:bg-[#386650]/10 rounded-lg transition duration-150"
                                                   title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition duration-150"
                                                            title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($transactions->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>

    {{-- Include Modals --}}
    @include('keuangan::web.transactions.create')

    @foreach ($transactions as $transaction)
        @include('keuangan::web.transactions.edit', ['transaction' => $transaction])
    @endforeach

</x-app-layout>
