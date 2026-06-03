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

        Transaction::create($data);

        $label = $data['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran';

        return redirect()
            ->route('dashboard')
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
            ->route('dashboard')
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
            ->route('dashboard')
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
