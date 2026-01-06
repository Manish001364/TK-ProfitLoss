<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = PnlExpenseCategory::forUser(auth()->id())
            ->withCount('expenses')
            ->ordered()
            ->get();

        return view('pnl.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('pnl.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['fixed', 'variable'])],
            'description' => 'nullable|string',
            'default_budget_limit' => 'nullable|numeric|min:0',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['sort_order'] = PnlExpenseCategory::forUser(auth()->id())->max('sort_order') + 1;

        $category = PnlExpenseCategory::create($validated);

        return redirect()
            ->route('pnl.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function edit(PnlExpenseCategory $category)
    {
        $this->authorize('update', $category);
        
        return view('pnl.categories.edit', compact('category'));
    }

    public function update(Request $request, PnlExpenseCategory $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['fixed', 'variable'])],
            'description' => 'nullable|string',
            'default_budget_limit' => 'nullable|numeric|min:0',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $category->update($validated);

        return redirect()
            ->route('pnl.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(PnlExpenseCategory $category)
    {
        $this->authorize('delete', $category);

        // Check if category has expenses
        if ($category->expenses()->exists()) {
            return back()->with('error', 'Cannot delete category with existing expenses.');
        }

        $category->delete();

        return redirect()
            ->route('pnl.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'uuid|exists:pnl_expense_categories,id',
        ]);

        foreach ($request->categories as $index => $categoryId) {
            PnlExpenseCategory::where('id', $categoryId)
                ->where('user_id', auth()->id())
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
