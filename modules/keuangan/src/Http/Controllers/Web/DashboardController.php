<?php

namespace Modules\Keuangan\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Keuangan\Models\SavingGoal;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with transaction summary and listing.
     */
    public function oldIndex(Request $request): View
    {
        $query = $request->user()
            ->transactions()
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan tipe
        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        // Filter berdasarkan kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $transactions = $query->paginate(15)->withQueryString();

        $categories = $request->user()
            ->categories()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        // Ringkasan saldo keseluruhan (tidak terpengaruh filter)
        $totalIncome  = $request->user()->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $request->user()->transactions()->where('type', 'expense')->sum('amount');
        $balance      = $totalIncome - $totalExpense;

        // ============================================
        // Data Chart: 30 hari terakhir
        // ============================================
        $startDate = Carbon::today()->subDays(29);
        $endDate   = Carbon::today();
        $period    = CarbonPeriod::create($startDate, $endDate);

        // Ambil sum harian pemasukan
        $dailyIncome = $request->user()
            ->transactions()
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Ambil sum harian pengeluaran
        $dailyExpense = $request->user()
            ->transactions()
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Susun data per hari dengan running balance
        $chartLabels    = [];
        $chartIncome    = [];
        $chartExpense   = [];
        $chartBalance   = [];
        $runningBalance = 0;

        // Hitung saldo sebelum 30 hari terakhir sebagai starting balance
        $priorIncome  = $request->user()->transactions()
            ->where('type', 'income')
            ->where('transaction_date', '<', $startDate)
            ->sum('amount');
        $priorExpense = $request->user()->transactions()
            ->where('type', 'expense')
            ->where('transaction_date', '<', $startDate)
            ->sum('amount');
        $runningBalance = $priorIncome - $priorExpense;

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayIncome  = (float) ($dailyIncome[$dateStr] ?? 0);
            $dayExpense = (float) ($dailyExpense[$dateStr] ?? 0);

            $runningBalance += $dayIncome - $dayExpense;

            $chartLabels[]  = $date->translatedFormat('d M');
            $chartIncome[]  = $dayIncome;
            $chartExpense[] = $dayExpense;
            $chartBalance[] = $runningBalance;
        }

        // ============================================
        // Target Tabungan (Saving Goal)
        // ============================================
        $savingGoal = $request->user()->savingGoals()->latest()->first();

        return view(keuangan_view('web.dashboard.index'), compact(
            'transactions',
            'categories',
            'totalIncome',
            'totalExpense',
            'balance',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'chartBalance',
            'savingGoal'
        ));
    }

    public function index(Request $request): View
    {
        // --- MULTI-WALLET LOGIC ---
        $activeWalletId = $request->session()->get('active_wallet_id');
        
        // Ensure user has at least one wallet
        if ($request->user()->wallets()->count() === 0) {
            $wallet = $request->user()->wallets()->create([
                'name' => 'Dompet Utama',
                'initial_balance' => 0,
                'color' => '#386650',
            ]);
            $activeWalletId = $wallet->id;
            $request->session()->put('active_wallet_id', $activeWalletId);
            
            // Migrate existing transactions
            $request->user()->transactions()->whereNull('wallet_id')->update(['wallet_id' => $wallet->id]);
        } else {
            if (!$activeWalletId || !$request->user()->wallets()->where('id', $activeWalletId)->exists()) {
                $activeWalletId = $request->user()->wallets()->first()->id;
                $request->session()->put('active_wallet_id', $activeWalletId);
            }
            
            // Just in case there are still orphaned transactions, assign to active
            $request->user()->transactions()->whereNull('wallet_id')->update(['wallet_id' => $activeWalletId]);
        }

        $activeWallet = $request->user()->wallets()->find($activeWalletId);
        $allWallets = $request->user()->wallets()->get();
        // --------------------------

        // Transaksi terbaru (untuk "Your Transfers") filter by active wallet
        $transactions = $request->user()
            ->transactions()
            ->where('wallet_id', $activeWalletId)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $categories = $request->user()
            ->categories()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $periodFilter = $request->query('period', 'bulan');

        switch ($periodFilter) {
            case 'hari':
                $startDate = Carbon::today();
                $endDate   = Carbon::today()->endOfDay();
                $periodLabel = 'Hari Ini';
                break;
            case 'minggu':
                $startDate = Carbon::today()->subDays(6)->startOfDay();
                $endDate   = Carbon::today()->endOfDay();
                $periodLabel = 'Minggu Ini';
                break;
            case 'tahun':
                $startDate = Carbon::today()->startOfYear();
                $endDate   = Carbon::today()->endOfYear();
                $periodLabel = 'Tahun Ini';
                break;
            case 'bulan':
            default:
                $periodFilter = 'bulan';
                $startDate = Carbon::today()->subDays(29)->startOfDay();
                $endDate   = Carbon::today()->endOfDay();
                $periodLabel = 'Bulan Ini';
                break;
        }

        // Ringkasan saldo (Filtered by period & wallet)
        $totalIncome  = $request->user()->transactions()->where('wallet_id', $activeWalletId)->where('type', 'income')
            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');
        $totalExpense = $request->user()->transactions()->where('wallet_id', $activeWalletId)->where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');

        // Saldo all-time
        $allTimeIncome = $request->user()->transactions()->where('wallet_id', $activeWalletId)->where('type', 'income')->sum('amount');
        $allTimeExpense = $request->user()->transactions()->where('wallet_id', $activeWalletId)->where('type', 'expense')->sum('amount');
        $balance = $activeWallet->initial_balance + $allTimeIncome - $allTimeExpense;

        // Data chart
        $transactionsForChart = $request->user()->transactions()
            ->where('wallet_id', $activeWalletId)
            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $chartLabels  = [];
        $chartIncome  = [];
        $chartExpense = [];
        $chartBalance = [];

        $priorIncome  = $request->user()->transactions()->where('wallet_id', $activeWalletId)->where('type', 'income')->where('transaction_date', '<', $startDate->format('Y-m-d'))->sum('amount');
        $priorExpense = $request->user()->transactions()->where('wallet_id', $activeWalletId)->where('type', 'expense')->where('transaction_date', '<', $startDate->format('Y-m-d'))->sum('amount');
        $runningBalance = $activeWallet->initial_balance + $priorIncome - $priorExpense;

        if ($periodFilter === 'hari') {
            $startHour = Carbon::today()->startOfDay();
            for ($i = 0; $i < 24; $i++) {
                $currentHour = $startHour->copy()->addHours($i);
                $nextHour = $currentHour->copy()->addHour();

                $dayIncome = $transactionsForChart->filter(function($tx) use ($currentHour, $nextHour) {
                    return $tx->type === 'income' && $tx->created_at >= $currentHour && $tx->created_at < $nextHour;
                })->sum('amount');
                
                $dayExpense = $transactionsForChart->filter(function($tx) use ($currentHour, $nextHour) {
                    return $tx->type === 'expense' && $tx->created_at >= $currentHour && $tx->created_at < $nextHour;
                })->sum('amount');

                $runningBalance += $dayIncome - $dayExpense;

                $chartLabels[]  = $currentHour->format('H:i');
                $chartIncome[]  = $dayIncome;
                $chartExpense[] = $dayExpense;
                $chartBalance[] = $runningBalance;
            }
        } elseif ($periodFilter === 'tahun') {
            $period = CarbonPeriod::create($startDate, '1 month', $endDate);
            foreach ($period as $date) {
                $monthStr = $date->format('Y-m');
                $dayIncome = $transactionsForChart->filter(function($tx) use ($monthStr) {
                    return $tx->type === 'income' && $tx->transaction_date->format('Y-m') === $monthStr;
                })->sum('amount');

                $dayExpense = $transactionsForChart->filter(function($tx) use ($monthStr) {
                    return $tx->type === 'expense' && $tx->transaction_date->format('Y-m') === $monthStr;
                })->sum('amount');

                $runningBalance += $dayIncome - $dayExpense;

                $chartLabels[]  = $date->translatedFormat('M Y');
                $chartIncome[]  = $dayIncome;
                $chartExpense[] = $dayExpense;
                $chartBalance[] = $runningBalance;
            }
        } else {
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $dayIncome = $transactionsForChart->filter(function($tx) use ($dateStr) {
                    return $tx->type === 'income' && $tx->transaction_date->format('Y-m-d') === $dateStr;
                })->sum('amount');

                $dayExpense = $transactionsForChart->filter(function($tx) use ($dateStr) {
                    return $tx->type === 'expense' && $tx->transaction_date->format('Y-m-d') === $dateStr;
                })->sum('amount');

                $runningBalance += $dayIncome - $dayExpense;

                $chartLabels[]  = $date->translatedFormat('d M');
                $chartIncome[]  = $dayIncome;
                $chartExpense[] = $dayExpense;
                $chartBalance[] = $runningBalance;
            }
        }

        $savingGoal = $request->user()->savingGoals()->latest()->first();

        return view(keuangan_view('web.dashboard.index'), compact(
            'transactions',
            'categories',
            'totalIncome',
            'totalExpense',
            'allTimeIncome',
            'allTimeExpense',
            'balance',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'chartBalance',
            'savingGoal',
            'periodFilter',
            'periodLabel',
            'activeWallet',
            'allWallets'
        ));
    }

    /**
     * Store or update the user's saving goal.
     */
    public function storeSavingGoal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:150'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
        ], [
            'name.required'          => 'Nama target wajib diisi.',
            'target_amount.required' => 'Nominal target wajib diisi.',
            'target_amount.min'      => 'Nominal target minimal Rp 1.',
            'start_date.required'    => 'Tanggal mulai wajib diisi.',
            'end_date.required'      => 'Tanggal akhir wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal mulai.',
        ]);

        $existingGoal = $request->user()->savingGoals()->latest()->first();

        if ($existingGoal) {
            $existingGoal->update($validated);
        } else {
            $request->user()->savingGoals()->create($validated);
        }

        return redirect()->route('dashboard')->with('success', 'Target tabungan berhasil disimpan!');
    }

    /**
     * Delete the user's saving goal.
     */
    public function deleteSavingGoal(Request $request): RedirectResponse
    {
        $request->user()->savingGoals()->delete();

        return redirect()->route('dashboard')->with('success', 'Target tabungan berhasil dihapus.');
    }
}
