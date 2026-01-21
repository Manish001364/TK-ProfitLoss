<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        // Get system default categories (read-only)
        $systemCategories = collect();
        try {
            $systemCategories = DB::table('pnl_expense_categories_system')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($item) {
                    $item->is_system = true;
                    $item->expenses_count = DB::table('pnl_expenses')
                        ->where('category_id', $item->id)
                        ->count();
                    return $item;
                });
        } catch (\Exception $e) {
            // Table might not exist yet
        }

        // Get user's custom categories
        $userCategories = PnlExpenseCategory::where('user_id', $userId)
            ->withCount('expenses')
            ->ordered()
            ->get()
            ->map(function ($item) {
                $item->is_system = false;
                return $item;
            });

        // Also get legacy categories with NULL user_id (system defaults from old schema)
        $legacyDefaults = PnlExpenseCategory::whereNull('user_id')
            ->where('is_active', true)
            ->withCount('expenses')
            ->ordered()
            ->get()
            ->map(function ($item) {
                $item->is_system = true;
                return $item;
            });

        // Combine all categories (system first, then user)
        $categories = $systemCategories->merge($legacyDefaults)->merge($userCategories);

        return view('pnl.categories.index', compact('categories', 'systemCategories', 'userCategories'));
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
        $validated['sort_order'] = PnlExpenseCategory::where('user_id', auth()->id())->max('sort_order') + 1;

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
