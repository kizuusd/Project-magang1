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
    public function index(Request $request): View
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
