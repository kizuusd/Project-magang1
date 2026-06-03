<?php

namespace Modules\Keuangan\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Keuangan\Http\Requests\CategoryRequest;
use Modules\Keuangan\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the user's categories.
     */
    public function index(Request $request): View
    {
        $categories = $request->user()
            ->categories()
            ->withCount('transactions')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view(keuangan_view('web.categories.index'), compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view(keuangan_view('web.categories.create'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $request->user()->categories()->create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        $this->authorizeCategory($category);

        return view(keuangan_view('web.categories.edit'), compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorizeCategory($category);

        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorizeCategory($category);

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }

    /**
     * Authorize that the authenticated user owns the category.
     */
    private function authorizeCategory(Category $category): void
    {
        if ($category->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke kategori ini.');
        }
    }
}
