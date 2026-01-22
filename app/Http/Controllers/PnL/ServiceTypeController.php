<?php
/**
 * Service Type Controller
 * 
 * Manages user's custom vendor service types.
 * System default types (Artist, DJ, Venue, etc.) are read-only.
 * 
 * Routes:
 * - GET  /pnl/service-types/create  -> create()
 * - POST /pnl/service-types         -> store()
 * - GET  /pnl/service-types/{id}/edit -> edit()
 * - PUT  /pnl/service-types/{id}    -> update()
 * - DELETE /pnl/service-types/{id}  -> destroy()
 * 
 * Note: index() redirects to combined Configuration page
 */

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ServiceTypeController extends Controller
{
    /**
     * Redirect to combined Configuration page
     */
    public function index()
    {
        return redirect()->route('pnl.configuration.index');
    }

    /**
     * Show form to create a new custom service type
     */
    public function create()
    {
        return view('pnl.service-types.create');
    }

    /**
     * Store a new custom service type
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
        ]);

        $userId = auth()->id();
        
        // Generate slug from name
        $slug = Str::slug($validated['name']);
        
        // Check for duplicate slug
        $existingSlug = DB::table('pnl_service_types_user')
            ->where('user_id', $userId)
            ->where('slug', $slug)
            ->exists();
            
        if ($existingSlug) {
            $slug = $slug . '-' . time();
        }

        // Insert into user service types table
        try {
            DB::table('pnl_service_types_user')->insert([
                'id' => (string) Str::uuid(),
                'user_id' => $userId,
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'] ?? null,
                'color' => $validated['color'],
                'icon' => $validated['icon'] ?? 'fas fa-user',
                'is_active' => true,
                'sort_order' => DB::table('pnl_service_types_user')->where('user_id', $userId)->max('sort_order') + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Fallback: use PnlServiceType model if table doesn't exist
            PnlServiceType::createForUser($userId, [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'color' => $validated['color'],
                'icon' => $validated['icon'] ?? 'fas fa-user',
            ]);
        }

        return redirect()
            ->route('pnl.configuration.index')
            ->with('success', 'Service type created successfully!');
    }

    /**
     * Show form to edit a custom service type
     */
    public function edit($id)
    {
        $userId = auth()->id();
        
        // Get from user table
        $serviceType = DB::table('pnl_service_types_user')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$serviceType) {
            return redirect()
                ->route('pnl.configuration.index')
                ->with('error', 'Service type not found or you cannot edit system defaults.');
        }

        return view('pnl.service-types.edit', compact('serviceType'));
    }

    /**
     * Update a custom service type
     */
    public function update(Request $request, $id)
    {
        $userId = auth()->id();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
        ]);

        // Update in user table
        $updated = DB::table('pnl_service_types_user')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'color' => $validated['color'],
                'icon' => $validated['icon'] ?? 'fas fa-user',
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return redirect()
                ->route('pnl.configuration.index')
                ->with('error', 'Service type not found or you cannot edit system defaults.');
        }

        return redirect()
            ->route('pnl.configuration.index')
            ->with('success', 'Service type updated successfully!');
    }

    /**
     * Delete a custom service type
     */
    public function destroy($id)
    {
        $userId = auth()->id();

        // Delete from user table
        $deleted = DB::table('pnl_service_types_user')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();

        if (!$deleted) {
            return redirect()
                ->route('pnl.configuration.index')
                ->with('error', 'Service type not found or cannot delete system defaults.');
        }

        return redirect()
            ->route('pnl.configuration.index')
            ->with('success', 'Service type deleted successfully!');
    }
}
