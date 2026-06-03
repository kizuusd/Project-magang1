<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('transactions.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:bg-gray-800 active:bg-black focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Transaksi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Welcome Card --}}
            <div class="bg-white border border-gray-200 overflow-hidden sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-1">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-500 text-sm">Kelola keuangan pribadi Anda dengan mudah dan terorganisir.</p>
                </div>
            </div>

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-emerald-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Ringkasan Saldo --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-emerald-100 rounded-lg p-2.5">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Pemasukan</p>
                            <p class="text-xl font-bold text-emerald-600">{{ format_rupiah($totalIncome) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-rose-100 rounded-lg p-2.5">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Pengeluaran</p>
                            <p class="text-xl font-bold text-rose-600">{{ format_rupiah($totalExpense) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 {{ $balance >= 0 ? 'bg-blue-100' : 'bg-orange-100' }} rounded-lg p-2.5">
                            <svg class="w-5 h-5 {{ $balance >= 0 ? 'text-blue-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Saldo</p>
                            <p class="text-xl font-bold {{ $balance >= 0 ? 'text-blue-600' : 'text-orange-600' }}">{{ format_rupiah($balance) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Diagram Statistik 30 Hari --}}
            <div class="bg-white border border-gray-200 overflow-hidden sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Statistik Keuangan</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Pemasukan, pengeluaran, dan saldo harian — 30 hari terakhir</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs">
                            <span class="flex items-center gap-1.5">
                                <span class="w-3 h-0.5 rounded bg-emerald-500 inline-block"></span>
                                Pemasukan
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-3 h-0.5 rounded bg-rose-500 inline-block"></span>
                                Pengeluaran
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-3 h-0.5 rounded bg-blue-500 inline-block"></span>
                                Saldo
                            </span>
                        </div>
                    </div>
                    <div class="relative" style="height: 320px;">
                        <canvas id="chartKeuangan"></canvas>
                    </div>
                </div>
            </div>

            {{-- Filter --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label for="filter-type" class="block text-xs font-medium text-gray-600 mb-1">Tipe</label>
                        <select id="filter-type" name="type"
                                class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">
                            <option value="">Semua Tipe</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <label for="filter-category" class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                        <select id="filter-category" name="category_id"
                                class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">
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
                                class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition duration-150">
                            Filter
                        </button>
                        @if (request()->hasAny(['type', 'category_id']))
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition duration-150">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabel Transaksi --}}
            <div class="bg-white border border-gray-200 overflow-hidden sm:rounded-lg">
                @if ($transactions->isEmpty())
                    <div class="text-center py-16">
                        <svg class="mx-auto w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                        </svg>
                        <p class="text-gray-500 text-sm font-medium">Belum ada transaksi</p>
                        <p class="text-gray-400 text-xs mt-1">Mulai tambahkan pemasukan atau pengeluaran Anda</p>
                        <div class="mt-4 flex justify-center gap-2">
                            <a href="{{ route('transactions.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 transition">
                                + Tambah Transaksi
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($transactions as $transaction)
                                    <tr class="hover:bg-gray-50 transition duration-100">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $transaction->transaction_date->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $transaction->category?->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                            {{ $transaction->description ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($transaction->type === 'income')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                    Pemasukan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                                    Pengeluaran
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $transaction->type === 'income' ? '+' : '-' }} {{ format_rupiah($transaction->amount) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{ route('transactions.edit', $transaction) }}"
                                                   class="inline-flex items-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition duration-150"
                                                   title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition duration-150"
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

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('chartKeuangan').getContext('2d');

            const labels   = @json($chartLabels);
            const income   = @json($chartIncome);
            const expense  = @json($chartExpense);
            const balance  = @json($chartBalance);

            // Fungsi format Rupiah singkat (1.5jt, 500rb, dll)
            function formatRupiahShort(value) {
                if (Math.abs(value) >= 1000000) {
                    return 'Rp ' + (value / 1000000).toFixed(1) + ' jt';
                } else if (Math.abs(value) >= 1000) {
                    return 'Rp ' + (value / 1000).toFixed(0) + ' rb';
                }
                return 'Rp ' + value.toLocaleString('id-ID');
            }

            function formatRupiahFull(value) {
                return 'Rp ' + Number(value).toLocaleString('id-ID', { minimumFractionDigits: 0 });
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: income,
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: 'rgb(16, 185, 129)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        },
                        {
                            label: 'Pengeluaran',
                            data: expense,
                            borderColor: 'rgb(244, 63, 94)',
                            backgroundColor: 'rgba(244, 63, 94, 0.08)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: 'rgb(244, 63, 94)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        },
                        {
                            label: 'Saldo',
                            data: balance,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.06)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 5,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleColor: '#f3f4f6',
                            bodyColor: '#f3f4f6',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { size: 13, weight: '600' },
                            bodyFont: { size: 12 },
                            displayColors: true,
                            boxWidth: 8,
                            boxHeight: 8,
                            boxPadding: 4,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.dataset.label + ': ' + formatRupiahFull(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                font: { size: 11 },
                                color: '#9ca3af',
                                maxTicksLimit: 10,
                            },
                            border: {
                                display: false,
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(229, 231, 235, 0.5)',
                                drawBorder: false,
                            },
                            ticks: {
                                font: { size: 11 },
                                color: '#9ca3af',
                                callback: function(value) {
                                    return formatRupiahShort(value);
                                },
                                maxTicksLimit: 6,
                            },
                            border: {
                                display: false,
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
