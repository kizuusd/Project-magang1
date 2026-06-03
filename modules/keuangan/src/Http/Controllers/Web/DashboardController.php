<?php

namespace Modules\Keuangan\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        return view(keuangan_view('web.dashboard.index'), compact(
            'transactions',
            'categories',
            'totalIncome',
            'totalExpense',
            'balance'
        ));
    }
}
