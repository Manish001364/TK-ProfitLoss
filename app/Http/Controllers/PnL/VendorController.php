<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlVendor;
use App\Exports\VendorsExport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        $query = PnlVendor::forUser($userId);

        // Filters
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sorting
        $sortBy = $request->get('sort', 'full_name');
        $sortDir = $request->get('dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $vendors = $query->paginate(15)->withQueryString();
        $vendorTypes = PnlVendor::getTypes();

        return view('pnl.vendors.index', compact('vendors', 'vendorTypes'));
    }

    public function create()
    {
        $vendorTypes = PnlVendor::getTypes();
        return view('pnl.vendors.create', compact('vendorTypes'));
    }

    public function store(Request $request)
    {
        // For quick add (AJAX), use simplified validation
        $isQuickAdd = $request->has('_quick_add');
        
        if ($isQuickAdd) {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'type' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'specialization' => 'nullable|string|max:255',
            ]);
            
            $validated['type'] = $validated['type'] ?? 'vendor';
            $validated['user_id'] = auth()->id();
            $validated['is_active'] = true;
            
            $vendor = PnlVendor::create($validated);
            
            return response()->json([
                'success' => true,
                'vendor' => [
                    'id' => $vendor->id,
                    'display_name' => $vendor->display_name,
                    'type' => $vendor->type,
                ],
                'message' => 'Vendor created successfully'
            ]);
        }
        
        // Full form validation
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'type' => ['nullable', Rule::in(array_keys(PnlVendor::getTypes()))],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'alternate_phone' => 'nullable|string|max:50',
            'business_address' => 'nullable|string',
            'home_address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_ifsc_code' => 'nullable|string|max:50',
            'bank_branch' => 'nullable|string|max:255',
            'tax_vat_reference' => 'nullable|string|max:100',
            'pan_number' => 'nullable|string|max:50',
            'gst_number' => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'preferred_payment_cycle' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['type'] = $validated['type'] ?? 'vendor';
        $validated['user_id'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        $vendor = PnlVendor::create($validated);

        return redirect()
            ->route('pnl.vendors.show', $vendor)
            ->with('success', 'Vendor/Artist created successfully!');
    }

    public function show(PnlVendor $vendor)
    {
        $this->authorize('view', $vendor);

        $vendor->load(['expenses.event', 'payments', 'attachments']);

        // Calculate vendor summary
        $summary = [
            'total_paid' => $vendor->total_paid,
            'total_pending' => $vendor->total_pending,
            'total_expenses' => $vendor->expenses->sum('total_amount'),
            'events_count' => $vendor->expenses->pluck('event_id')->unique()->count(),
        ];

        return view('pnl.vendors.show', compact('vendor', 'summary'));
    }

    public function edit(PnlVendor $vendor)
    {
        $this->authorize('update', $vendor);
        
        $vendorTypes = PnlVendor::getTypes();
        return view('pnl.vendors.edit', compact('vendor', 'vendorTypes'));
    }

    public function update(Request $request, PnlVendor $vendor)
    {
        $this->authorize('update', $vendor);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'type' => ['required', Rule::in(array_keys(PnlVendor::getTypes()))],
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'business_address' => 'nullable|string',
            'home_address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc_code' => 'nullable|string|max:20',
            'bank_branch' => 'nullable|string|max:255',
            'tax_vat_reference' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'preferred_payment_cycle' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $vendor->update($validated);

        return redirect()
            ->route('pnl.vendors.show', $vendor)
            ->with('success', 'Vendor/Artist updated successfully!');
    }

    public function destroy(PnlVendor $vendor)
    {
        $this->authorize('delete', $vendor);

        $vendor->delete();

        return redirect()
            ->route('pnl.vendors.index')
            ->with('success', 'Vendor/Artist deleted successfully!');
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        $filename = 'vendors_' . now()->format('Y-m-d_His');

        return Excel::download(
            new VendorsExport(auth()->id(), $request->all()),
            $filename . '.' . $format
        );
    }
}
