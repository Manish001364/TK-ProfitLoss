<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $serviceTypes = PnlServiceType::getAllForUser($userId);
        
        return view('pnl.service-types.index', compact('serviceTypes'));
    }

    public function create()
    {
        return view('pnl.service-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $userId = auth()->id();
        $slug = Str::slug($validated['name']);
        
        // Check if slug already exists for this user
        $existsInSystem = DB::table('pnl_service_types_system')
            ->where('slug', $slug)
            ->exists();
            
        $existsForUser = DB::table('pnl_service_types_user')
            ->where('user_id', $userId)
            ->where('slug', $slug)
            ->exists();
            
        if ($existsInSystem || $existsForUser) {
            return back()->withInput()->with('error', 'A service type with this name already exists.');
        }

        DB::table('pnl_service_types_user')->insert([
            'id' => Str::uuid()->toString(),
            'user_id' => $userId,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'fas fa-tag',
            'color' => $validated['color'],
            'sort_order' => DB::table('pnl_service_types_user')
                ->where('user_id', $userId)
                ->max('sort_order') + 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('pnl.service-types.index')
            ->with('success', 'Service type created successfully!');
    }

    public function edit($id)
    {
        $userId = auth()->id();
        
        $serviceType = DB::table('pnl_service_types_user')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
            
        if (!$serviceType) {
            return redirect()
                ->route('pnl.service-types.index')
                ->with('error', 'Service type not found or you cannot edit system defaults.');
        }
        
        return view('pnl.service-types.edit', compact('serviceType'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ]);

        $userId = auth()->id();
        
        $serviceType = DB::table('pnl_service_types_user')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
            
        if (!$serviceType) {
            return redirect()
                ->route('pnl.service-types.index')
                ->with('error', 'Service type not found or you cannot edit system defaults.');
        }

        DB::table('pnl_service_types_user')
            ->where('id', $id)
            ->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'icon' => $validated['icon'] ?? 'fas fa-tag',
                'color' => $validated['color'],
                'is_active' => $request->boolean('is_active', true),
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('pnl.service-types.index')
            ->with('success', 'Service type updated successfully!');
    }

    public function destroy($id)
    {
        $userId = auth()->id();
        
        $serviceType = DB::table('pnl_service_types_user')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
            
        if (!$serviceType) {
            return redirect()
                ->route('pnl.service-types.index')
                ->with('error', 'Service type not found or you cannot delete system defaults.');
        }

        // Check if any vendors use this type
        $vendorsCount = DB::table('pnl_vendors')
            ->where('user_id', $userId)
            ->where('type', $serviceType->slug)
            ->count();
            
        if ($vendorsCount > 0) {
            return back()->with('error', "Cannot delete this service type. {$vendorsCount} vendor(s) are using it.");
        }

        DB::table('pnl_service_types_user')
            ->where('id', $id)
            ->delete();

        return redirect()
            ->route('pnl.service-types.index')
            ->with('success', 'Service type deleted successfully!');
    }
}
