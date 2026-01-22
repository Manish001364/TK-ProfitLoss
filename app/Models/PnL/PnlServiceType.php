<?php

namespace App\Models\PnL;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PnlServiceType extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pnl_service_types_user';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all service types (system defaults + user created) for a user
     */
    public static function getAllForUser($userId)
    {
        // Get system default types
        $systemTypes = collect();
        try {
            $systemTypes = DB::table('pnl_service_types_system')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($item) {
                    $item->is_system = true;
                    $item->vendors_count = DB::table('pnl_vendors')
                        ->where('type', $item->slug)
                        ->count();
                    return $item;
                });
        } catch (\Exception $e) {
            // Table might not exist yet - return hardcoded defaults
            $systemTypes = collect(self::getDefaultTypes())->map(function ($item, $slug) {
                return (object) array_merge($item, [
                    'slug' => $slug,
                    'is_system' => true,
                    'vendors_count' => 0,
                ]);
            })->values();
        }

        // Get user's custom types
        $userTypes = collect();
        try {
            $userTypes = DB::table('pnl_service_types_user')
                ->where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($item) {
                    $item->is_system = false;
                    $item->vendors_count = DB::table('pnl_vendors')
                        ->where('type', $item->slug)
                        ->count();
                    return $item;
                });
        } catch (\Exception $e) {
            // Table might not exist yet
        }

        // Combine all types (system first, then user)
        return $systemTypes->merge($userTypes);
    }

    /**
     * Get service types as key-value array for dropdowns
     */
    public static function getTypesForDropdown($userId): array
    {
        $allTypes = self::getAllForUser($userId);
        $result = [];
        
        foreach ($allTypes as $type) {
            $result[$type->slug] = $type->name;
        }
        
        return $result;
    }

    /**
     * Default service types (hardcoded fallback)
     */
    public static function getDefaultTypes(): array
    {
        return [
            'artist' => ['name' => 'Artist', 'icon' => 'fas fa-music', 'color' => '#dc3545'],
            'dj' => ['name' => 'DJ', 'icon' => 'fas fa-headphones', 'color' => '#6f42c1'],
            'venue' => ['name' => 'Venue', 'icon' => 'fas fa-building', 'color' => '#0dcaf0'],
            'catering' => ['name' => 'Catering', 'icon' => 'fas fa-utensils', 'color' => '#fd7e14'],
            'security' => ['name' => 'Security', 'icon' => 'fas fa-shield-alt', 'color' => '#6c757d'],
            'equipment' => ['name' => 'Equipment Hire', 'icon' => 'fas fa-cogs', 'color' => '#20c997'],
            'marketing' => ['name' => 'Marketing', 'icon' => 'fas fa-bullhorn', 'color' => '#d63384'],
            'staff' => ['name' => 'Staff', 'icon' => 'fas fa-users', 'color' => '#198754'],
            'transport' => ['name' => 'Transport', 'icon' => 'fas fa-truck', 'color' => '#0d6efd'],
            'photography' => ['name' => 'Photography', 'icon' => 'fas fa-camera', 'color' => '#ffc107'],
            'decor' => ['name' => 'Decor', 'icon' => 'fas fa-paint-brush', 'color' => '#17a2b8'],
            'mc' => ['name' => 'MC/Host', 'icon' => 'fas fa-microphone', 'color' => '#6610f2'],
            'other' => ['name' => 'Other', 'icon' => 'fas fa-ellipsis-h', 'color' => '#adb5bd'],
        ];
    }

    /**
     * Get a service type by slug
     */
    public static function getBySlug($userId, $slug)
    {
        // Check system types first
        try {
            $systemType = DB::table('pnl_service_types_system')
                ->where('slug', $slug)
                ->first();
            if ($systemType) {
                $systemType->is_system = true;
                return $systemType;
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Check user types
        try {
            $userType = DB::table('pnl_service_types_user')
                ->where('user_id', $userId)
                ->where('slug', $slug)
                ->first();
            if ($userType) {
                $userType->is_system = false;
                return $userType;
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Fallback to hardcoded
        $defaults = self::getDefaultTypes();
        if (isset($defaults[$slug])) {
            return (object) array_merge($defaults[$slug], [
                'slug' => $slug,
                'is_system' => true,
            ]);
        }

        return null;
    }

    /**
     * Get a service type by slug OR ID (for flexibility)
     */
    public static function getBySlugOrId($userId, $slugOrId)
    {
        // First try by slug
        $result = self::getBySlug($userId, $slugOrId);
        if ($result) {
            return $result;
        }

        // Then try by ID in system types
        try {
            $systemType = DB::table('pnl_service_types_system')
                ->where('id', $slugOrId)
                ->first();
            if ($systemType) {
                $systemType->is_system = true;
                return $systemType;
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Then try by ID in user types
        try {
            $userType = DB::table('pnl_service_types_user')
                ->where('id', $slugOrId)
                ->where('user_id', $userId)
                ->first();
            if ($userType) {
                $userType->is_system = false;
                return $userType;
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        return null;
    }

    /**
     * Create a custom service type for a user
     */
    public static function createForUser($userId, array $data): self
    {
        $data['user_id'] = $userId;
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? (self::where('user_id', $userId)->max('sort_order') + 1);
        
        return self::create($data);
    }
}
