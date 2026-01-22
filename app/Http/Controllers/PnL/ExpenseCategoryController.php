<?php
/**
 * Expense Category Controller
 * 
 * Manages user's custom expense categories.
 * System default categories are read-only and managed separately.
 * 
 * Routes:
 * - GET  /pnl/categories/create  -> create()
 * - POST /pnl/categories         -> store()
 * - GET  /pnl/categories/{id}/edit -> edit()
 * - PUT  /pnl/categories/{id}    -> update()
 * - DELETE /pnl/categories/{id}  -> destroy()
 * 
 * Note: index() redirects to combined Configuration page
 */

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    /**
     * Redirect to combined Configuration page
     */
    public function index()
    {
        return redirect()->route('pnl.configuration.index');
    }

    /**
     * Show form to create a new custom category
     */
    public function create()
    {
        return view('pnl.categories.create');
    }

    /**
     * Store a new custom category
     */
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

        $userId = auth()->id();
        $validated['user_id'] = $userId;
        $validated['sort_order'] = PnlExpenseCategory::where('user_id', $userId)->max('sort_order') + 1;

        PnlExpenseCategory::create($validated);

        return redirect()
            ->route('pnl.configuration.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show form to edit a custom category
     */
    public function edit(PnlExpenseCategory $category)
    {
        $this->authorize('update', $category);
        
        // Cannot edit system categories
        if ($category->user_id === null) {
            return redirect()
                ->route('pnl.configuration.index')
                ->with('error', 'System categories cannot be edited.');
        }

        return view('pnl.categories.edit', compact('category'));
    }

    /**
     * Update a custom category
     */
    public function update(Request $request, PnlExpenseCategory $category)
    {
        $this->authorize('update', $category);
        
        // Cannot edit system categories
        if ($category->user_id === null) {
            return redirect()
                ->route('pnl.configuration.index')
                ->with('error', 'System categories cannot be edited.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['fixed', 'variable'])],
            'description' => 'nullable|string',
            'default_budget_limit' => 'nullable|numeric|min:0',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
        ]);

        $category->update($validated);

        return redirect()
            ->route('pnl.configuration.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete a custom category (only if not in use)
     */
    public function destroy(PnlExpenseCategory $category)
    {
        $this->authorize('delete', $category);
        
        // Cannot delete system categories
        if ($category->user_id === null) {
            return redirect()
                ->route('pnl.configuration.index')
                ->with('error', 'System categories cannot be deleted.');
        }

        // Check if category is in use
        if ($category->expenses()->count() > 0) {
            return redirect()
                ->route('pnl.configuration.index')
                ->with('error', 'Cannot delete category - it has expenses assigned to it.');
        }

        $category->delete();

        return redirect()
            ->route('pnl.configuration.index')
            ->with('success', 'Category deleted successfully!');
    }

    /**
     * Reorder categories (AJAX)
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|string',
            'categories.*.order' => 'required|integer',
        ]);

        $userId = auth()->id();

        foreach ($validated['categories'] as $item) {
            PnlExpenseCategory::where('id', $item['id'])
                ->where('user_id', $userId)
                ->update(['sort_order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
