<?php

namespace Modules\Keuangan\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WalletController extends Controller
{
    /**
     * Switch the active wallet.
     */
    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
        ]);

        // Ensure the wallet belongs to the user
        $wallet = $request->user()->wallets()->findOrFail($validated['wallet_id']);

        Session::put('active_wallet_id', $wallet->id);

        return redirect()->back()->with('success', 'Dompet aktif diubah ke ' . $wallet->name);
    }

    /**
     * Store a newly created wallet.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initial_balance' => 'nullable|numeric',
        ]);

        $wallet = $request->user()->wallets()->create([
            'name' => $validated['name'],
            'initial_balance' => $validated['initial_balance'] ?? 0,
            'color' => '#386650', // Default color
        ]);

        // If it's their first wallet, make it active
        if (!$request->session()->has('active_wallet_id')) {
            $request->session()->put('active_wallet_id', $wallet->id);
        }

        return redirect()->back()->with('success', 'Dompet berhasil ditambahkan.');
    }

    /**
     * Update the specified wallet.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $wallet = $request->user()->wallets()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initial_balance' => 'nullable|numeric',
        ]);

        $wallet->update([
            'name' => $validated['name'],
            'initial_balance' => $validated['initial_balance'] ?? 0,
        ]);

        return redirect()->back()->with('success', 'Dompet berhasil diperbarui.');
    }

    /**
     * Remove the specified wallet.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $wallet = $request->user()->wallets()->findOrFail($id);

        // Don't allow deleting if it's the only wallet
        if ($request->user()->wallets()->count() <= 1) {
            return redirect()->back()->withErrors(['error' => 'Anda tidak dapat menghapus satu-satunya dompet Anda.']);
        }

        $wallet->delete();

        // If the active wallet was deleted, unset session
        if (Session::get('active_wallet_id') == $id) {
            Session::forget('active_wallet_id');
        }

        return redirect()->back()->with('success', 'Dompet berhasil dihapus.');
    }
}
