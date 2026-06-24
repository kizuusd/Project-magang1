<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard — {{ config('app.name', 'Radiohead Wallet') }}</title>
    <meta name="description" content="Personal Finance Dashboard — kelola keuangan Anda dengan mudah.">

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">

    {{-- Vite Assets (Tailwind CSS + Alpine.js) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Anti-FOUC: apply dark class BEFORE page renders --}}
    <script>
        (function() {
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <style>
        /* ─── Base ─── */
        * { font-family: 'Inter', sans-serif; }
        body { background-color: var(--app-bg); }

        /* ─── Smooth scroll & scrollbar ─── */
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #C8C5BE; border-radius: 99px; }

        /* ─── Card hover lift ─── */
        .card { transition: transform .2s ease, box-shadow .2s ease; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0,0,0,.07); }

        /* ─── Semi-circle donut (CSS only) ─── */
        .donut-wrap {
            position: relative;
            width: 176px;
            height: 88px;
            overflow: hidden;
            margin: 0 auto;
        }
        .donut-wrap::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 176px;
            height: 176px;
            border-radius: 50%;
            /* Rotate -90deg so 0deg (top) starts at the left */
            transform: rotate(-90deg);
            background: conic-gradient(
                #386650 0deg var(--donut-fill, 144deg),
                #DCE8DE var(--donut-fill, 144deg) 360deg
            );
        }
        /* inner cutout */
        .donut-wrap::after {
            content: '';
            position: absolute;
            bottom: 0; left: 50%;
            transform: translateX(-50%);
            width: 116px; height: 58px;
            background: var(--card-bg);
            border-radius: 116px 116px 0 0;
        }
        .donut-label {
            position: absolute;
            bottom: 2px; left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
            white-space: nowrap;
        }

        /* ─── Stacked credit cards ─── */
        .card-stack { position: relative; width: 175px; height: 150px; flex-shrink: 0; }
        .credit-card {
            position: absolute;
            width: 145px; height: 88px;
            border-radius: 14px;
            padding: 10px 12px;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .credit-card-num { font-size: 6.5px; letter-spacing: .12em; color: rgba(255,255,255,.55); }
        .credit-card-label { font-size: 6px; letter-spacing: .14em; text-transform: uppercase; color: rgba(255,255,255,.55); }
        .mc-circles { display: flex; align-items: center; }
        .mc-circle { width: 16px; height: 16px; border-radius: 50%; }



        /* ─── Transfer item left border ─── */
        .transfer-item { border-left: 3px solid #D1D5DB; padding-left: 10px; }
        .transfer-item.income-border { border-color: #386650; }
        .transfer-item.expense-border { border-color: #EF4444; }
    </style>
</head>

<body class="antialiased min-h-screen">

@php
    /* ── Computed helpers ── */
    // all-time fallback (jika variabel tidak ada, gunakan period total)
    $allTimeIncome  = $allTimeIncome  ?? $totalIncome  ?? 0;
    $allTimeExpense = $allTimeExpense ?? $totalExpense ?? 0;

    // expensePct = berapa % pengeluaran dari pemasukan (bukan dari total)
    $expensePct   = $allTimeIncome > 0 ? round(($allTimeExpense / $allTimeIncome) * 100) : 0;
    $incomePct    = 100; // pemasukan selalu 100% acuan
    // donut: cap di 180deg (100% = seluruh setengah lingkaran)
    $donutDeg     = round((min($expensePct, 100) / 100) * 180);
    $balancePositive = ($balance ?? 0) >= 0;
    $recentTx     = isset($transactions) ? $transactions->take(3) : collect();
@endphp

<div class="flex min-h-screen relative">

    <x-sidebar />

    {{-- ═══════════════════════════════════════════════ --}}
    {{--   MAIN CONTENT                                  --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <main class="flex-1 px-6 py-6 lg:px-8 lg:py-7 overflow-x-hidden lg:ml-64">

        {{-- Flash message --}}
        @if(session('success'))
            <div class="mb-4 flex items-center gap-2 bg-[#386650]/10 border border-[#386650]/20 text-[#386650] text-sm font-medium px-4 py-2.5 rounded-xl">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ──────────────────────────────────── --}}
        {{--   HEADER                             --}}
        {{-- ──────────────────────────────────── --}}
        <header class="flex items-center justify-between mb-7">
            {{-- Left: logo + greeting --}}
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


        </header>


        {{-- ═══════════════════════════════════════════════ --}}
        {{--   ROW 1 — KPI CARDS                            --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">

            {{-- 2. New clients → Total Transaksi --}}
            <div class="card bg-white rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#F4F3F0] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 mb-0.5">Total Transaksi</p>
                            <p class="text-[1.6rem] font-bold text-gray-900 leading-none">{{ isset($transactions) ? number_format($transactions->total()) : '0' }}</p>
                        </div>
                    </div>
                    {{-- mini line --}}
                    <svg class="w-14 h-9 text-[#6a9e78] shrink-0" viewBox="0 0 56 36" fill="none">
                        <polyline points="2,32 10,24 20,28 30,14 40,18 48,8 54,12"
                                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>

            {{-- 3. Earnings → Total Pemasukan (All-time) --}}
            <div class="card bg-white rounded-2xl p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
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

            {{-- 4. Activity → Saldo (dark green bg) --}}
            <div class="card rounded-2xl p-5 shadow-sm relative overflow-hidden" style="background-color:#386650;">
                <div class="relative z-10">
                    <p class="text-xs font-medium text-[#B9D1BF] mb-1.5">Saldo Bersih</p>
                    <p class="text-[1.6rem] font-bold text-white leading-none">{{ format_rupiah($balance ?? 0) }}</p>
                </div>
                {{-- mini squiggly line --}}
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-20 h-12 opacity-30" viewBox="0 0 80 48" fill="none">
                    <path d="M2 38 Q10 36 16 28 T32 22 Q44 18 50 22 T66 10 L78 14"
                          stroke="white" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                </svg>
                <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            </div>
        </div>


        {{-- ═══════════════════════════════════════════════ --}}
        {{--   ROW 2 — BALANCE / EARNINGS / PROFILE         --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-4">

            {{-- ─── Balance area chart card (wide) ─── --}}
            <div class="lg:col-span-9 card bg-white rounded-2xl p-6 shadow-sm flex flex-col">
                {{-- header row --}}
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <h2 class="text-[15px] font-bold text-gray-900">Saldo</h2>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#386650]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#386650] inline-block"></span>
                            On track
                        </span>
                    </div>
                    <div class="relative inline-block">
                        <select onchange="window.location.href='?period='+this.value" class="appearance-none bg-gray-100 text-[11px] font-medium text-gray-500 pl-3 pr-7 py-1.5 rounded-lg cursor-pointer hover:bg-gray-200/60 transition border-0 focus:ring-0">
                            <option value="hari" {{ ($periodFilter ?? '') == 'hari' ? 'selected' : '' }}>Harian</option>
                            <option value="minggu" {{ ($periodFilter ?? '') == 'minggu' ? 'selected' : '' }}>Mingguan</option>
                            <option value="bulan" {{ ($periodFilter ?? 'bulan') == 'bulan' ? 'selected' : '' }}>Bulanan</option>
                            <option value="tahun" {{ ($periodFilter ?? '') == 'tahun' ? 'selected' : '' }}>Tahunan</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                    </div>
                </div>

                {{-- sub-info chips --}}
                <div class="flex gap-3 mb-5">
                    <div class="flex-1 bg-gray-100 rounded-xl px-4 py-3">
                        <p class="text-[10px] text-gray-400 mb-1 font-medium">Pemasukan</p>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-[15px] font-bold text-gray-900">{{ format_rupiah($totalIncome ?? 0) }}</span>
                            @if(($totalIncome ?? 0) > 0)
                                <span class="text-[10px] font-bold text-[#386650]">+{{ $expensePct > 0 ? (100 - min($expensePct, 100)) : 100 }}% sisa</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 bg-gray-100 rounded-xl px-4 py-3">
                        <p class="text-[10px] text-gray-400 mb-1 font-medium">Saldo</p>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-[15px] font-bold text-gray-900">{{ format_rupiah($balance ?? 0) }}</span>
                            @if($allTimeIncome > 0)
                                @php $balancePct = round(($balance / $allTimeIncome) * 100); @endphp
                                <span class="text-[10px] font-bold {{ $balancePositive ? 'text-[#386650]' : 'text-red-400' }}">
                                    {{ $balancePositive ? '+' : '' }}{{ $balancePct }}%
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- chart canvas --}}
                <div class="flex-1" style="min-height:160px; position:relative;">
                    <canvas id="balanceChart"></canvas>
                </div>
            </div>

            {{-- ─── Earnings / Total Expense card ─── --}}
            <div class="lg:col-span-3 card bg-white rounded-2xl p-6 shadow-sm flex flex-col">
                <h2 class="text-[15px] font-bold text-gray-900 mb-0.5">Pengeluaran</h2>
                <p class="text-[11px] text-gray-400 mb-2">Total All-time</p>
                <p class="text-[1.7rem] font-bold text-[#386650] mb-1 leading-tight">{{ format_rupiah($allTimeExpense) }}</p>
                @php
                    $expenseIncomePctChange = ($allTimeIncome > 0)
                        ? round((($allTimeExpense / $allTimeIncome) - 1) * 100)
                        : 0;
                @endphp
                <p class="text-[11px] text-gray-400 mb-5">
                    @if($expensePct >= 100)
                        ⚠️ Pengeluaran melebihi pemasukan ({{ $expensePct }}%)
                    @elseif($expensePct > 70)
                        Pengeluaran {{ $expensePct }}% dari total pemasukan
                    @else
                        Pengeluaran terkontrol ({{ $expensePct }}% dari pemasukan)
                    @endif
                </p>

                {{-- semi-circle donut --}}
                <div class="flex-1 flex flex-col items-center justify-end">
                    <div class="donut-wrap" style="--donut-fill: {{ $donutDeg }}deg;">
                        <span class="donut-label">{{ $expensePct }}%</span>
                    </div>
                </div>
            </div>

        </div>


        {{-- ═══════════════════════════════════════════════ --}}
        {{--   ROW 3 — CREDIT CARD / TRANSFERS / SECURITY   --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            {{-- ─── Target Tabungan (Credit Card aesthetics) ─── --}}
            <div class="lg:col-span-5 card bg-white rounded-2xl p-6 shadow-sm overflow-hidden">
                <div class="flex items-start gap-4">
                    {{-- left text --}}
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-bold text-gray-900 leading-snug mb-2">
                            Target Tabungan<br>
                            @if(isset($savingGoal) && $savingGoal)
                                <span class="text-[#386650]">{{ $savingGoal->name }}</span>
                            @else
                                di Dompet Anda
                            @endif
                        </h2>

                        @if(isset($savingGoal) && $savingGoal)
                            @php
                                $goalProgress = $savingGoal->target_amount > 0
                                    ? min(100, round(($balance / $savingGoal->target_amount) * 100, 1))
                                    : 0;
                            @endphp
                            <p class="text-[11px] text-gray-400 leading-relaxed mb-1">
                                Target: <span class="font-semibold text-gray-700">{{ format_rupiah($savingGoal->target_amount) }}</span>
                            </p>
                            <p class="text-[11px] text-gray-400 mb-1">
                                Sampai: <span class="font-semibold text-gray-700">{{ $savingGoal->end_date->translatedFormat('d M Y') }}</span>
                            </p>
                            {{-- mini progress bar --}}
                            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4 mt-3">
                                <div class="h-1.5 rounded-full bg-[#386650] transition-all duration-700"
                                     style="width: {{ max(3, $goalProgress) }}%"></div>
                            </div>
                            <p class="text-[11px] text-gray-400 mb-4">Progress <span class="font-bold text-[#386650]">{{ $goalProgress }}%</span></p>
                        @else
                            <p class="text-xs text-gray-400 leading-relaxed mb-5">
                                Tetapkan target tabungan Anda. Kami akan membantu memantau progres keuangan Anda setiap hari.
                            </p>
                        @endif

                        @if(isset($savingGoal) && $savingGoal)
                            <button id="btn-edit-goal" onclick="document.getElementById('modal-saving-goal').classList.remove('hidden')"
                                    class="inline-flex items-center px-4 py-2.5 bg-[#386650] text-white text-xs font-semibold rounded-xl hover:bg-[#2d5241] transition-colors shadow-sm">
                                Edit Target
                            </button>
                        @else
                            <button id="btn-add-goal" onclick="document.getElementById('modal-saving-goal').classList.remove('hidden')"
                                    class="inline-flex items-center gap-1 px-4 py-2.5 bg-[#386650] text-white text-xs font-semibold rounded-xl hover:bg-[#2d5241] transition-colors shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Tambah Target +
                            </button>
                        @endif
                    </div>

                    {{-- right: stacked credit cards (CSS) --}}
                    <div class="card-stack">
                        {{-- card 3 (back) --}}
                        <div class="credit-card shadow-md"
                             style="top:0; right:0; background: linear-gradient(135deg,#4a5568,#2d3748); transform: rotate(7deg) translateY(6px) translateX(4px); opacity:.65;">
                            <div class="credit-card-label">Master Card</div>
                            <div>
                                <div style="width:22px;height:15px;border-radius:3px;background:rgba(253,224,71,.5);"></div>
                            </div>
                            <div class="credit-card-num">•••• •••• •••• 4532</div>
                            <div class="mc-circles" style="position:absolute;bottom:8px;right:10px;">
                                <div class="mc-circle" style="background:rgba(239,68,68,.55);"></div>
                                <div class="mc-circle" style="background:rgba(252,211,77,.55);margin-left:-8px;"></div>
                            </div>
                        </div>
                        {{-- card 2 (mid) --}}
                        <div class="credit-card shadow-lg"
                             style="top:16px; right:6px; background: linear-gradient(135deg,#5a7a6c,#386650); transform: rotate(2.5deg) translateY(2px); opacity:.8;">
                            <div class="credit-card-label">Master Card</div>
                            <div>
                                <div style="width:22px;height:15px;border-radius:3px;background:rgba(253,224,71,.6);"></div>
                            </div>
                            <div class="credit-card-num">•••• •••• •••• 8721</div>
                            <div class="mc-circles" style="position:absolute;bottom:8px;right:10px;">
                                <div class="mc-circle" style="background:rgba(239,68,68,.6);"></div>
                                <div class="mc-circle" style="background:rgba(252,211,77,.6);margin-left:-8px;"></div>
                            </div>
                        </div>
                        {{-- card 1 (front) --}}
                        <div class="credit-card shadow-xl"
                             style="top:32px; right:12px; background: linear-gradient(135deg,#2d5241,#1e362b); transform: rotate(-3deg);">
                            <div class="credit-card-label" style="color:rgba(255,255,255,.7);">Master Card</div>
                            <div>
                                <div style="width:22px;height:15px;border-radius:3px;background:rgba(253,224,71,.75);"></div>
                            </div>
                            <div class="credit-card-num" style="color:rgba(255,255,255,.65);">•••• •••• •••• 3156</div>
                            <div class="mc-circles" style="position:absolute;bottom:8px;right:10px;">
                                <div class="mc-circle" style="background:rgba(239,68,68,.7);"></div>
                                <div class="mc-circle" style="background:rgba(252,211,77,.7);margin-left:-8px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── Your Transfers (last 3 transactions) ─── --}}
            <div class="lg:col-span-4 card bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-[15px] font-bold text-gray-900">Transaksi Terbaru</h2>
                </div>

                @if($recentTx->isEmpty())
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <svg class="w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                        </svg>
                        <p class="text-xs text-gray-400">Belum ada transaksi</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($recentTx as $tx)
                            <div class="transfer-item {{ $tx->type === 'income' ? 'income-border' : 'expense-border' }}">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[13px] font-semibold text-gray-800 truncate max-w-[160px]">
                                            {{ $tx->type === 'income' ? 'Dari' : 'Ke' }}
                                            {{ $tx->category?->name ?? ($tx->description ?? 'Transaksi') }}
                                        </p>
                                        <p class="text-[10px] text-gray-400">
                                            {{ $tx->transaction_date->translatedFormat('d M') }},
                                            {{ $tx->created_at->format('H:i') }}
                                        </p>
                                    </div>
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-lg
                                        {{ $tx->type === 'income'
                                            ? 'bg-[#386650]/10 text-[#386650]'
                                            : 'bg-red-50 text-red-500' }}">
                                        {{ $tx->type === 'income' ? '+' : '-' }}{{ format_rupiah($tx->amount) }}
                                    </span>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <div class="border-t border-gray-50"></div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ─── Security card ─── --}}
            <div class="lg:col-span-3 card bg-white rounded-2xl p-6 shadow-sm flex flex-col items-center justify-center text-center">
                {{-- Fingerprint SVG --}}
                <div class="mb-4 text-[#386650]/65">
                    <svg class="w-[60px] h-[60px]" viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                        {{-- outer arcs --}}
                        <path d="M10 30c0-11 9-20 20-20s20 9 20 20" opacity=".2"/>
                        <path d="M15 30c0-8.3 6.7-15 15-15s15 6.7 15 15" opacity=".35"/>
                        <path d="M20 30c0-5.5 4.5-10 10-10s10 4.5 10 10" opacity=".55"/>
                        <path d="M25 30c0-2.8 2.2-5 5-5s5 2.2 5 5" opacity=".75"/>
                        {{-- outer arcs going down --}}
                        <path d="M10 30c0 11 9 20 20 20" opacity=".2"/>
                        <path d="M15 30c0 8.3 6.7 15 15 15" opacity=".35"/>
                        <path d="M20 30c0 5.5 4.5 10 10 10" opacity=".55"/>
                        {{-- center dot --}}
                        <circle cx="30" cy="30" r="2.5" fill="currentColor" opacity=".9"/>
                    </svg>
                </div>
                <h3 class="text-[15px] font-bold text-gray-900 mb-1">Keep you safe!</h3>
                <p class="text-[11px] text-gray-400 mb-5 leading-relaxed">Update your security password</p>
                <a href="{{ route('profile.edit') }}" id="btn-update-security"
                   class="inline-flex items-center px-5 py-2.5 bg-[#386650] text-white text-xs font-semibold rounded-xl hover:bg-[#2d5241] transition-colors shadow-sm">
                    Update Your Security
                </a>
            </div>
        </div>

    </main>
</div>{{-- end flex --}}


{{-- ═══════════════════════════════════════════════════ --}}
{{--   MODAL — Saving Goal                               --}}
{{-- ═══════════════════════════════════════════════════ --}}
<div id="modal-saving-goal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    {{-- backdrop --}}
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"
         onclick="document.getElementById('modal-saving-goal').classList.add('hidden')"></div>
    {{-- panel --}}
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">
                {{ isset($savingGoal) && $savingGoal ? 'Edit Target Tabungan' : 'Tambah Target Tabungan' }}
            </h3>
            <button type="button" id="modal-close-btn"
                    onclick="document.getElementById('modal-saving-goal').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('saving-goal.store') }}" method="POST" class="p-6 space-y-4" id="form-saving-goal">
            @csrf
            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 p-3 text-xs text-red-600 space-y-0.5">
                    @foreach ($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
                </div>
            @endif

            <div>
                <label for="sg-name" class="block text-xs font-medium text-gray-700 mb-1.5">Nama Target <span class="text-red-400">*</span></label>
                <input type="text" id="sg-name" name="name"
                       value="{{ old('name', isset($savingGoal) ? $savingGoal->name : 'Target Tabungan') }}"
                       class="w-full rounded-xl border-gray-300 text-sm focus:border-[#386650] focus:ring-[#386650]">
            </div>
            <div>
                <label for="sg-amount" class="block text-xs font-medium text-gray-700 mb-1.5">Nominal Target (Rp) <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-sm text-gray-400 pointer-events-none">Rp</span>
                    <input type="number" id="sg-amount" name="target_amount" min="1"
                           value="{{ old('target_amount', isset($savingGoal) ? (int)$savingGoal->target_amount : '') }}"
                           class="w-full pl-9 rounded-xl border-gray-300 text-sm focus:border-[#386650] focus:ring-[#386650]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="sg-start" class="block text-xs font-medium text-gray-700 mb-1.5">Tanggal Mulai <span class="text-red-400">*</span></label>
                    <input type="date" id="sg-start" name="start_date"
                           value="{{ old('start_date', isset($savingGoal) ? $savingGoal->start_date->format('Y-m-d') : date('Y-m-d')) }}"
                           class="w-full rounded-xl border-gray-300 text-sm focus:border-[#386650] focus:ring-[#386650]">
                </div>
                <div>
                    <label for="sg-end" class="block text-xs font-medium text-gray-700 mb-1.5">Tanggal Akhir <span class="text-red-400">*</span></label>
                    <input type="date" id="sg-end" name="end_date"
                           value="{{ old('end_date', isset($savingGoal) ? $savingGoal->end_date->format('Y-m-d') : '') }}"
                           class="w-full rounded-xl border-gray-300 text-sm focus:border-[#386650] focus:ring-[#386650]">
                </div>
            </div>
            <div class="flex justify-end gap-2.5 pt-2 border-t border-gray-100">
                <button type="button"
                        onclick="document.getElementById('modal-saving-goal').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" id="btn-save-goal"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#386650] rounded-xl hover:bg-[#2d5241] transition">
                    Simpan Target
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════ --}}
{{--   SCRIPTS                                           --}}
{{-- ═══════════════════════════════════════════════════ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ─── Balance Area Chart ─── */
    const balCtx = document.getElementById('balanceChart');
    if (balCtx) {
        const labels  = @json($chartLabels ?? []);
        const balance = @json($chartBalance ?? []);
        const income  = @json($chartIncome  ?? []);
        const expense = @json($chartExpense ?? []);

        const makeGradient = (ctx, r, g, b) => {
            const { chartArea } = ctx.chart;
            if (!chartArea) return `rgba(${r},${g},${b},0.12)`;
            const gr = ctx.chart.ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
            gr.addColorStop(0, `rgba(${r},${g},${b},0.18)`);
            gr.addColorStop(1, `rgba(${r},${g},${b},0.01)`);
            return gr;
        };

        new Chart(balCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Saldo',
                        data: balance,
                        borderColor: '#386650',
                        backgroundColor: (ctx) => makeGradient(ctx, 56, 102, 80),
                        borderWidth: 2,
                        fill: true,
                        tension: 0.42,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#386650',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                    },
                    {
                        label: 'Pemasukan',
                        data: income,
                        borderColor: '#6a9e78',
                        backgroundColor: 'transparent',
                        borderWidth: 1.5,
                        borderDash: [4, 3],
                        fill: false,
                        tension: 0.42,
                        pointRadius: 0,
                    },
                    {
                        label: 'Pengeluaran',
                        data: expense,
                        borderColor: '#EF4444',
                        backgroundColor: 'transparent',
                        borderWidth: 1.5,
                        borderDash: [4, 3],
                        fill: false,
                        tension: 0.42,
                        pointRadius: 0,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e362b',
                        titleColor: '#DCE8DE',
                        bodyColor: '#B9D1BF',
                        cornerRadius: 10,
                        padding: 10,
                        displayColors: true,
                        callbacks: {
                            label: ctx => ' ' + new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(ctx.raw)
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            color: '#B0ADA6',
                            font: { size: 10 },
                            maxTicksLimit: 8,
                            maxRotation: 0,
                        }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.035)', drawBorder: false },
                        border: { display: false },
                        ticks: { display: false }
                    }
                },
                interaction: { intersect: false, mode: 'index' }
            }
        });
    }

    /* ─── Saving Goal form date validation ─── */
    const goalForm = document.getElementById('form-saving-goal');
    if (goalForm) {
        goalForm.addEventListener('submit', function(e) {
            const start = document.getElementById('sg-start').value;
            const end   = document.getElementById('sg-end').value;
            if (start && end && start > end) {
                e.preventDefault();
                alert('Tanggal akhir tidak boleh sebelum tanggal mulai.');
            }
        });
    }

    /* ─── Auto-open modal if there are validation errors ─── */
    @if($errors->any())
        document.getElementById('modal-saving-goal')?.classList.remove('hidden');
    @endif

    /* ─── Progress bar animation ─── */
    document.querySelectorAll('[data-progress]').forEach(el => {
        const target = parseFloat(el.getAttribute('data-progress'));
        setTimeout(() => { el.style.width = target + '%'; }, 200);
    });

});
</script>

</body>
</html>
