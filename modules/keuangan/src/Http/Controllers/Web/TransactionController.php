<?php

namespace Modules\Keuangan\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Keuangan\Http\Requests\TransactionRequest;
use Modules\Keuangan\Models\Category;
use Modules\Keuangan\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request): View
    {
        $activeWalletId = $request->session()->get('active_wallet_id');
        
        $query = $request->user()
            ->transactions()
            ->where('wallet_id', $activeWalletId)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $transactions = $query->paginate(15)->withQueryString();

        $categories = $request->user()
            ->categories()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $allTimeIncome = $request->user()->transactions()->where('wallet_id', $activeWalletId)->where('type', 'income')->sum('amount');
        $allTimeExpense = $request->user()->transactions()->where('wallet_id', $activeWalletId)->where('type', 'expense')->sum('amount');
        $totalTransactions = $request->user()->transactions()->where('wallet_id', $activeWalletId)->count();
        $balance = $allTimeIncome - $allTimeExpense;
        
        $activeWallet = $request->user()->wallets()->find($activeWalletId);
        if ($activeWallet) {
            $balance += $activeWallet->initial_balance;
        }

        return view(keuangan_view('web.transactions.index'), compact(
            'transactions', 
            'categories', 
            'allTimeIncome', 
            'allTimeExpense', 
            'totalTransactions', 
            'balance'
        ));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(Request $request): View
    {
        $categories = $request->user()
            ->categories()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        // Pre-select tipe dari query string
        $selectedType = $request->query('type', 'expense');
        if (! in_array($selectedType, ['income', 'expense'])) {
            $selectedType = 'expense';
        }

        // Pre-select kategori dari query string
        $selectedCategoryId = $request->query('category_id');

        return view(keuangan_view('web.transactions.create'), compact(
            'categories',
            'selectedType',
            'selectedCategoryId'
        ));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(TransactionRequest $request): RedirectResponse
    {
        // Pastikan category_id milik user ini
        if ($request->category_id) {
            $category = Category::find($request->category_id);
            if (! $category || $category->user_id !== $request->user()->id) {
                return back()->withErrors(['category_id' => 'Kategori tidak valid.'])->withInput();
            }
        }

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['wallet_id'] = $request->session()->get('active_wallet_id');

        Transaction::create($data);

        $label = $data['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran';

        return redirect()
            ->route('transactions.index')
            ->with('success', "{$label} berhasil ditambahkan!");
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Request $request, Transaction $transaction): View
    {
        $this->authorizeTransaction($transaction);

        $categories = $request->user()
            ->categories()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view(keuangan_view('web.transactions.edit'), compact('transaction', 'categories'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeTransaction($transaction);

        // Pastikan category_id milik user ini
        if ($request->category_id) {
            $category = Category::find($request->category_id);
            if (! $category || $category->user_id !== $request->user()->id) {
                return back()->withErrors(['category_id' => 'Kategori tidak valid.'])->withInput();
            }
        }

        $transaction->update($request->validated());

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui!');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorizeTransaction($transaction);

        $transaction->delete();

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }

    /**
     * Authorize that the authenticated user owns the transaction.
     */
    private function authorizeTransaction(Transaction $transaction): void
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }
    }
}
